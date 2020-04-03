<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use Shopware\Production\Merchants\Content\Merchant\Exception\OrderAlreadyCompletedException;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class OrderController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StateMachineRegistry
     */
    private $stateMachineRegistry;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        EntityRepositoryInterface $orderRepository,
        StateMachineRegistry $stateMachineRegistry
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->orderRepository = $orderRepository;
        $this->stateMachineRegistry = $stateMachineRegistry;
    }

    /**
     * @Route(name="merchant-api.orders.load", path="/merchant-api/v{version}/orders")
     */
    public function load(MerchantEntity $merchant): JsonResponse
    {
        $criteria = new Criteria([$merchant->getId()]);
        $criteria->addAssociation('orders.deliveries');
        $criteria->addAssociation('orders.lineItems');

        /** @var MerchantEntity $merchant */
        $merchant = $this->merchantRepository->search($criteria, Context::createDefaultContext())->first();

        return new JsonResponse($merchant->getOrders());
    }

    /**
     * @Route(name="merchant-api.orders.detail", path="/merchant-api/v{version}/order/{orderId}")
     */
    public function detail(MerchantEntity $merchant, string $orderId): JsonResponse
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('merchants');
        $criteria->addFilter(new EqualsFilter('merchants.id', $merchant->getId()));

        $order = $this->orderRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order with ID \'%s\' couldn\'t be found', $orderId));
        }

        return new JsonResponse($order);
    }

    /**
     * @Route(name="merchant-api.orders.done", path="/merchant-api/v{version}/order/{orderId}/done", methods={"PATCH"})
     */
    public function done(MerchantEntity $merchant, string $orderId): JsonResponse
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('merchants');
        $criteria->addFilter(new EqualsFilter('merchants.id', $merchant->getId()));

        /** @var OrderEntity $order */
        $order = $this->orderRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order with ID \'%s\' couldn\'t be found', $orderId));
        }

        if ($order->getStateMachineState()->getTechnicalName() !== 'open') {
            throw new OrderAlreadyCompletedException(sprintf('Order with ID \'%s\' is already completed.', $orderId));
        }

        $this->stateMachineRegistry->transition(
            new Transition(
                'order',
                $order->getId(),
                'process',
                'stateId'
            ), Context::createDefaultContext()
        );

        $this->stateMachineRegistry->transition(
            new Transition(
                'order',
                $order->getId(),
                'complete',
                'stateId'
            ), Context::createDefaultContext()
        );

        return new JsonResponse([
            'success' => true
        ]);
    }
}
