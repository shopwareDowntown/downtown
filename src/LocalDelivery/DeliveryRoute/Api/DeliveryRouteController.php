<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\DeliveryRoute\Api;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Production\LocalDelivery\DeliveryRoute\Services\MapboxService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class DeliveryRouteController
{
    /**
     * @var MapboxService
     */
    private $mapboxService;

    /**
     * DeliveryRouteController constructor.
     */
    public function __construct(MapboxService $mapboxService)
    {
        $this->mapboxService = $mapboxService;
    }

    /**
     * @Route(name="delivery-api.route.generate", path="/delivery-api/route/generate")
     */
    public function generate() : JsonResponse
    {
        $result = $this->mapboxService->getGpsCoordinates("Some Adress");
        return new JsonResponse($result);
    }
}
