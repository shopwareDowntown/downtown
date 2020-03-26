<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\DeliveryRoute\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Production\LocalDelivery\DeliveryRoute\Services\DeliveryRouteService;
use Shopware\Production\LocalDelivery\DeliveryRoute\Services\MapboxService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class DeliveryRouteController
{
    /**
     * @var DeliveryRouteService
     */
    private $deliveryRouteService;

    /**
     * DeliveryRouteController constructor.
     */
    public function __construct(DeliveryRouteService $deliveryRouteService)
    {
        $this->deliveryRouteService = $deliveryRouteService;
    }

    /**
     * @Route(name="delivery-api.route.generate", path="/delivery-api/route/generate")
     */
    public function generate(Request $request, Context $context) : JsonResponse
    {
        $result = $this->deliveryRouteService->getNewestRoute($context);
        return new JsonResponse($result);
    }
}
