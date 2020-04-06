<?php

namespace Shopware\Production\Voucher\Subscriber;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Api\Exception\InvalidSalesChannelIdException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Voucher\Service\VoucherFundingMerchantService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderStateChangedSubscriber implements EventSubscriberInterface
{
    private $voucherFundingService;

    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        VoucherFundingMerchantService $voucherFundingService,
        EntityRepositoryInterface $orderRepository
    )
    {
        $this->voucherFundingService = $voucherFundingService;
        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_transaction.state.paid' => 'orderTransactionStatePaid',
        ];
    }

    public function orderTransactionStatePaid(OrderStateMachineStateChangeEvent $event) : void
    {
        $context = $event->getContext();
        $order = $this->getVoucherByOrderId($event->getOrder()->getId(), $context);
        if($order === null || $order->getLineItems()->count() === 0) {
            return;
        }

        $merchant = $this->fetchMerchantFromOrder($event->getOrder()->getId(), $context);

        $this->voucherFundingService->createSoldVoucher($merchant, $order, $context);
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
            ->addAssociation('transactions.stateMachineState');

        $criteria
            ->addFilter(new EqualsFilter('lineItems.product.customFields.productType', 'voucher'));

        /** @var OrderEntity $orderEntity */
        $orderEntity = $this->orderRepository->search($criteria, $context)->first();

        return $orderEntity;
    }

    private function fetchMerchantFromOrder(string $orderId, Context $context): MerchantEntity
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('merchants.country');

        /** @var OrderEntity $orderEntity */
        $orderEntity = $this->orderRepository->search($criteria, $context)->first();

        return $orderEntity->getExtension('merchants')->first();
    }
}
