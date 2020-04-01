<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class OrderListController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(EntityRepositoryInterface $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
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
}
