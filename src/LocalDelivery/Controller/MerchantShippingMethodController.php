<?php
/**
 * Created by PhpStorm.
 * User: m.sestendrup
 * Date: 2020-03-27
 * Time: 15:35
 */

namespace Shopware\Production\LocalDelivery\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MerchantShippingMethodController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantShippingMethodRepository;

    public function __construct(EntityRepositoryInterface $merchantShippingMethodRepository)
    {
        $this->merchantShippingMethodRepository = $merchantShippingMethodRepository;
    }

    /**
     * @Route("/merchant-api/v{version}/shipping-method", name="merchant-api.shipping-method.get", methods={"GET"})
     */
    public function getMerchantShippingMethods(Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('merchants');
        $shippingMethods = $this->merchantShippingMethodRepository->search($criteria, $context->getContext());

        return new JsonResponse($shippingMethods);
    }
}
