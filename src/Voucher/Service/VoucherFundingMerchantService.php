<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Service;

use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Voucher\Checkout\SoldVoucher\SoldVoucherEntity;
use Shopware\Production\Voucher\Checkout\SoldVoucher\VoucherStatuses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class VoucherFundingMerchantService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $soldVoucherRepository;

    private $voucherFundingEmailService;
    private $translator;

    public function __construct(
        EntityRepositoryInterface $soldVoucherRepository,
        VoucherFundingEmailService $voucherFundingEmailService,
        TranslatorInterface $translator
    ) {
        $this->soldVoucherRepository = $soldVoucherRepository;
        $this->voucherFundingEmailService = $voucherFundingEmailService;
        $this->translator = $translator;
    }

    public function loadSoldVouchers(string $merchantId, Request $request, SalesChannelContext $context): array
    {
        $limit = (int) $request->query->get('limit', 10);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('merchantId', $merchantId));
        $criteria->setLimit($limit);

        if ($request->query->has('page')) {
            $criteria->setOffset((int) $request->query->get('page') * $limit);
        } else {
            $criteria->setOffset((int) $request->query->get('offset', 0));
        }

        if ($request->query->has('status')) {
            if ($request->query->get('status') === VoucherStatuses::USED_VOUCHER) {
                $criteria->addFilter(new NotFilter(MultiFilter::CONNECTION_AND, [
                    new EqualsFilter('redeemedAt', null),
                ]));
            }

            if ($request->query->get('status') === VoucherStatuses::UNUSED_VOUCHER) {
                $criteria->addFilter(new EqualsFilter('redeemedAt', null));
            }
        }

        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        $soldVoucherCollection = $this->soldVoucherRepository->search($criteria, $context->getContext());
        $data = [];

        foreach ($soldVoucherCollection->getEntities() as $element) {
            $data[] = [
                'id' => $element->getId(),
                'updatedAt' => $element->getUpdatedAt(),
                'createdAt' => $element->getCreatedAt(),
                'redeemedAt' => $element->getRedeemedAt(),
                'status' => $element->getRedeemedAt() ? VoucherStatuses::USED_VOUCHER : VoucherStatuses::UNUSED_VOUCHER,
                'code' => $element->getCode(),
                'name' => $element->getName(),
                'orderLineItemId' => $element->getOrderLineItemId(),
                'value' => $element->getValue(),
            ];
        }

        return [
            'data' => $data,
            'total' => $soldVoucherCollection->getTotal(),
        ];
    }

    public function createSoldVoucher(
        MerchantEntity $merchant,
        OrderEntity $order,
        Context $context
    ): void {
        $merchantId = $merchant->getId();
        $vouchers = [];

        foreach ($order->getLineItems() as $lineItemEntity) {
            $voucherNum = $lineItemEntity->getQuantity();
            $lineItemPrice = $lineItemEntity->getPriceDefinition();

            $voucherValue = new AbsolutePriceDefinition($lineItemPrice->getPrice(), $lineItemPrice->getPrecision());

            for ($i = 0; $i < $voucherNum; ++$i) {
                $code = $this->generateUniqueVoucherCode($merchantId, $context);

                $voucher = [];
                $voucher['merchantId'] = $merchantId;
                $voucher['orderLineItemId'] = $lineItemEntity->getId();
                $voucher['name'] = $lineItemEntity->getProduct()->getTranslation('name');
                $voucher['code'] = $code;
                $voucher['value'] = $voucherValue;

                $vouchers[] = $voucher;
            }
        }

        $this->soldVoucherRepository->create($vouchers, $context);

        $this->notifyToMerchantAndCustomer($merchant, $order, $vouchers, $context);
    }

    public function redeemVoucher(string $voucherCode, MerchantEntity $merchant, SalesChannelContext $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('code', $voucherCode));
        $criteria->addFilter(new EqualsFilter('redeemedAt', null));
        $criteria->addFilter(new EqualsFilter('merchantId', $merchant->getId()));

        /** @var SoldVoucherEntity $voucher */
        $voucher = $this->soldVoucherRepository->search($criteria, $context->getContext())->first();

        if (!$voucher) {
            throw new NotFoundHttpException(sprintf('Cannot find valid voucher with code %s', $voucherCode));
        }

        $this->soldVoucherRepository->update([[
            'id' => $voucher->getId(),
            'redeemedAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]], $context->getContext());
    }

    public function getVoucherStatus(string $voucherCode, MerchantEntity $merchant, SalesChannelContext $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('code', $voucherCode));
        $criteria->addFilter(new EqualsFilter('merchantId', $merchant->getId()));
        $criteria->addAssociation('orderLineItem.order.customer');

        /** @var SoldVoucherEntity $voucher */
        $voucher = $this->soldVoucherRepository->search($criteria, $context->getContext())->first();

        if (empty($voucher)) {
            return [
                'status' => VoucherStatuses::INVALID_VOUCHER,
            ];
        }

        return [
            'customer' => $voucher->getOrderLineItem()->getOrder()->getOrderCustomer(),
            'status' => $voucher->getRedeemedAt() ? VoucherStatuses::USED_VOUCHER : VoucherStatuses::UNUSED_VOUCHER,
        ];
    }

    private function notifyToMerchantAndCustomer(MerchantEntity $merchant, OrderEntity $order, array $vouchers, Context $context): void
    {
        $customerName = sprintf('%s %s %s',
            $order->getOrderCustomer()->getSalutation()->getDisplayName(),
            $order->getOrderCustomer()->getFirstName(),
            $order->getOrderCustomer()->getLastName()
        );

        $templateData = [
            'merchant' => $merchant,
            'order' => $order,
            'vouchers' => $vouchers,
            'today' => (new \DateTime())->format(Defaults::STORAGE_DATE_FORMAT),
            'customerName' => $customerName,
            'locale' => $this->translator->getLocale()
        ];

        $this->voucherFundingEmailService->sendEmailCustomer($templateData, $merchant, $context);
        $this->voucherFundingEmailService->sendEmailMerchant($templateData, $merchant, $context);
    }

    private function generateUniqueVoucherCode(string $merchantId, Context $context)
    {
        $code = $this->generateVoucherCode();

        if ($this->checkCodeUnique($merchantId, $code, $context)) {
            return $code;
        }

        return $this->generateUniqueVoucherCode($merchantId, $context);
    }

    private function checkCodeUnique(string $merchantId, string $code, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('merchantId', $merchantId));
        $criteria->addFilter(new EqualsFilter('code', $code));

        return $this->soldVoucherRepository->search($criteria, $context)->count() === 0;
    }

    private function generateVoucherCode(int $length = 10)
    {
        return mb_strtoupper(Random::getAlphanumericString($length));
    }
}
