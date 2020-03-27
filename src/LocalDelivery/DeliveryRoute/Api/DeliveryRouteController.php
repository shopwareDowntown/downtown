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
    public const TRAVEL_PROFILES = [
        'driving',
        'walking',
        'cycling',
        'driving-traffic'
    ];

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
     * @Route(name="delivery-api.route.get.newest", path="/delivery-api/route/get-newest")
     */
    public function getNewestRoute(Request $request, Context $context) : JsonResponse
    {
        $deliveryBoyId = ""; // TODO: replace with logged in boy id
        $result = $this->deliveryRouteService->getNewestRoute($deliveryBoyId, $context);
        return new JsonResponse($result->getRouteWaypoints());
    }

    /**
     * @Route(name="delivery-api.route.generate", path="/delivery-api/route/generate")
     */
    public function generate(Request $request, Context $context) : JsonResponse
    {
        $merchantId = $request->query->get('merchantId');
        $travelProfile = $request->query->get('travelProfile');

        if ($merchantId === null) {
            throw new \Exception('merchantId not given.');
        }

        if ($travelProfile === null) {
            throw new \Exception('travelProfile not given (possible values are: '. implode(', ', self::TRAVEL_PROFILES) . ')');
        }

        if (!in_array($travelProfile, self::TRAVEL_PROFILES, true)) {
            throw new \Exception('travelProfile is not valid, choose one of the following values: '. implode(', ', self::TRAVEL_PROFILES));
        }

        $deliveryBoyId = ""; // TODO: replace with logged in boy id
        $result = $this->deliveryRouteService->generateRoute($deliveryBoyId, $merchantId, $travelProfile, $context);
        return new JsonResponse($result);
    }
}
