<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Subscriber;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Voucher\Service\VoucherFundingMerchantService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderStateChangedSubscriber implements EventSubscriberInterface
{
    private $voucherFundingService;

    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SalesChannelContextFactory
     */
    private $salesChannelContextFactory;

    /**
     * @var EntityRepositoryInterface
     */
    private $languageRepository;

    public function __construct(
        VoucherFundingMerchantService $voucherFundingService,
        EntityRepositoryInterface $orderRepository,
        TranslatorInterface $translator,
        SalesChannelContextFactory $salesChannelContextFactory,
        EntityRepositoryInterface $languageRepository
    ) {
        $this->voucherFundingService = $voucherFundingService;
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->languageRepository = $languageRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_transaction.state.paid' => 'orderTransactionPaid',
        ];
    }

    public function orderTransactionPaid(OrderStateMachineStateChangeEvent $event) : void
    {
        $salesChannelContext = $this->salesChannelContextFactory->create(Random::getAlphanumericString(16), $event->getSalesChannelId());

        $this->fixTranslatorLanguage($salesChannelContext, $event->getOrder()->getLanguageId());

        $order = $this->getVoucherByOrderId($event->getOrder()->getId(), $salesChannelContext->getContext());
        if($order === null || $order->getLineItems()->count() === 0) {
            return;
        }

        $merchant = $this->fetchMerchantFromOrder($event->getOrder()->getId(), $salesChannelContext->getContext());

        $this->voucherFundingService->createSoldVoucher($merchant, $order, $salesChannelContext->getContext());
    }

    private function getVoucherByOrderId(string $orderId, Context $context): ?OrderEntity
    {
        $criteria = new Criteria([$orderId]);
        $criteria
            ->addAssociation('orderCustomer.salutation')
            ->addAssociation('lineItems.product')
            ->addAssociation('lineItems.payload')
            ->addAssociation('cartPrice.calculatedTaxes')
            ->addAssociation('currency')
            ->addAssociation('salesChannel')
            ->addAssociation('addresses')
            ->addAssociation('deliveries')
            ->addAssociation('transactions.stateMachineState');

        $criteria
            ->addFilter(new EqualsFilter('lineItems.type', LineItem::PRODUCT_LINE_ITEM_TYPE))
            ->addFilter(new EqualsFilter('lineItems.product.customFields.productType', 'voucher'));

        /** @var OrderEntity $orderEntity */
        $orderEntity = $this->orderRepository->search($criteria, $context)->first();

        return $orderEntity;
    }

    private function fetchMerchantFromOrder(string $orderId, Context $context): MerchantEntity
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('merchants.cover');
        $criteria->addAssociation('merchants.country');

        /** @var OrderEntity $orderEntity */
        $orderEntity = $this->orderRepository->search($criteria, $context)->first();

        return $orderEntity->getExtension('merchants')->first();
    }

    private function fixTranslatorLanguage(SalesChannelContext $context, string $languageId): void
    {
        $language = $this->fetchLanguage($languageId);

        $this->translator->injectSettings(
            $context->getSalesChannel()->getId(),
            $languageId,
            $language->getLocale()->getCode(),
            $context->getContext()
        );
    }

    private function fetchLanguage(string $languageId): LanguageEntity
    {
        $criteria = new Criteria([$languageId]);
        $criteria->addAssociation('locale');

        return $this->languageRepository->search($criteria, Context::createDefaultContext())->first();
    }
}
