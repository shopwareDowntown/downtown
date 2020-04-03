<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use Shopware\Production\Merchants\Content\Merchant\Exception\OrderAlreadyCompletedException;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route(name="merchant-api.orders.load", path="/merchant-api/v{version}/orders", methods={"GET"})
     */
    public function load(MerchantEntity $merchant, Request $request): JsonResponse
    {
        $orderState = $request->query->getAlnum('state');
        $limit = $request->query->getInt('limit', 25);
        $offset = $request->query->getInt('offset', 0);

        $criteria = new Criteria();
        $criteria->addAssociation('deliveries');
        $criteria->addAssociation('lineItems');
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT)
            ->addFilter(new EqualsFilter('merchants.id', $merchant->getId()))
            ->setLimit($limit)
            ->setOffset($offset);

        if ($orderState !== '') {
            $criteria->addFilter(
                new EqualsFilter('stateMachineState.technicalName', $orderState)
            );
        }

        $orders = $this->orderRepository->search($criteria, Context::createDefaultContext());

        return new JsonResponse([
            'data' => $orders->getEntities(),
            'total' => $orders->getTotal()
        ]);
    }

    /**
     * @Route(name="merchant-api.orders.detail", path="/merchant-api/v{version}/order/{orderId}", methods={"GET"})
     */
    public function detail(MerchantEntity $merchant, string $orderId): JsonResponse
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addFilter(new EqualsFilter('merchants.id', $merchant->getId()));
        $criteria->addAssociation('deliveries');
        $criteria->addAssociation('lineItems');

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
