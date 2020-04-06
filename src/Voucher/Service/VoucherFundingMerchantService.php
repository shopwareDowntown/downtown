<?php

namespace Shopware\Production\Voucher\Service;

use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Voucher\Checkout\SoldVoucher\SoldVoucherEntity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VoucherFundingMerchantService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $soldVoucherRepository;

    private $voucherFundingEmailService;
    /**
     * @var EntityRepositoryInterface
     */
    private $currencyRepository;

    public function __construct(
        EntityRepositoryInterface $soldVoucherRepository,
        EntityRepositoryInterface $currencyRepository,
        VoucherFundingEmailService $voucherFundingEmailService
    ) {
        $this->soldVoucherRepository = $soldVoucherRepository;
        $this->voucherFundingEmailService = $voucherFundingEmailService;
        $this->currencyRepository = $currencyRepository;
    }

    public function loadSoldVouchers(string $merchantId, SalesChannelContext $context) : array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('merchantId', $merchantId));

        return $this->soldVoucherRepository->search($criteria, $context->getContext())->getElements();
    }

    public function createSoldVoucher(
        MerchantEntity $merchant,
        OrderEntity $order,
        Context $context
    ) : void
    {
        $merchantId = $merchant->getId();
        $vouchers = [];
        foreach ($order->getLineItems() as $lineItemEntity) {
            $voucherNum = $lineItemEntity->getQuantity();
            $lineItemPrice = $lineItemEntity->getPriceDefinition();
            $voucherValue = new AbsolutePriceDefinition($lineItemPrice->getPrice(), $lineItemPrice->getPrecision());

            for ($i = 0; $i < $voucherNum; $i++) {
                $code = $this->generateUniqueVoucherCode($merchantId, $context);

                $voucher = [];
                $voucher['merchantId'] = $merchantId;
                $voucher['orderLineItemId'] = $lineItemEntity->getId();
                $voucher['name'] = $lineItemEntity->getProduct()->getName();
                $voucher['code'] = $code;
                $voucher['value'] = $voucherValue;

                $vouchers[] = $voucher;
            }
        }

        $this->soldVoucherRepository->create($vouchers, $context);
        $this->voucherFundingEmailService->sendEmailMerchant($vouchers, $merchant, $order, $context);
        $this->voucherFundingEmailService->sendEmailCustomer($vouchers, $merchant, $order, $context);
    }

    public function redeemVoucher(string $voucherCode, MerchantEntity $merchant, SalesChannelContext $context) : void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('code', $voucherCode));
        $criteria->addFilter(new EqualsFilter('redeemedAt', null));
        $criteria->addFilter(new EqualsFilter('merchantId', $merchant->getId()));

        /** @var SoldVoucherEntity $voucher */
        $voucher = $this->soldVoucherRepository->search($criteria, $context->getContext())->first();

        if(!$voucher) {
            throw new NotFoundHttpException(sprintf('Cannot find valid voucher with code %s', $voucherCode));
        }

        $this->soldVoucherRepository->update([[
            'id' => $voucher->getId(),
            'redeemedAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
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

        $data = [
            'status' => 'invalid',
            'customer' => null
        ];

        if(!$voucher) {
            return $data;
        }

        $data['customer'] = $voucher->getOrderLineItem()->getOrder()->getOrderCustomer();

        $data['status'] = $voucher->getRedeemedAt() ? 'used' : 'valid';

        return $data;
    }

    private function generateUniqueVoucherCode(string $merchantId, Context $context)
    {
        $code = $this->generateVoucherCode();

        if($this->checkCodeUnique($merchantId, $code, $context)) {
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
