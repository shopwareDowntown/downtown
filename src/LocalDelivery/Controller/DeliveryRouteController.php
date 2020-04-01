<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\LocalDelivery\Services\DeliveryBoyLoginService;
use Shopware\Production\LocalDelivery\Services\DeliveryRouteService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @RouteScope(scopes={"storefront"})
 */
class DeliveryRouteController extends StorefrontController
{
    public const TRAVEL_PROFILES = [
        'driving',
        'walking',
        'cycling',
        'driving-traffic'
    ];

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DeliveryRouteService
     */
    private $deliveryRouteService;

    /**
     * @var DeliveryBoyLoginService
     */
    private $deliveryBoyLoginService;

    public function __construct(
        DeliveryRouteService $deliveryRouteService,
        DeliveryBoyLoginService $deliveryBoyLoginService,
        RouterInterface $router
    )
    {
        $this->deliveryRouteService = $deliveryRouteService;
        $this->deliveryBoyLoginService = $deliveryBoyLoginService;
        $this->router = $router;
    }

    /**
     * @Route(name="delivery-api.route.get.newest", path="/delivery-api/route/get-newest", methods={"GET"})
     */
    public function getNewestRoute(SalesChannelContext $salesChannelContext) : Response
    {
        if (!$this->deliveryBoyLoginService->isDeliveryBoyLoggedIn($salesChannelContext->getContext())) {
            return new RedirectResponse(
                $this->router->generate('delivery.boy.login.form')
            );
        }


        $deliveryBoyId = $this->deliveryBoyLoginService->getDeliveryBoyId();
        $result = $this->deliveryRouteService->getNewestRoute($deliveryBoyId, $salesChannelContext->getContext());
        return new JsonResponse($result->getRouteWaypoints());
    }

    /**
     * @Route(name="delivery-api.route.generate", path="/delivery-api/route/generate", methods={"GET"})
     */
    public function generate(Request $request, SalesChannelContext $salesChannelContext) : Response
    {
        if (!$this->deliveryBoyLoginService->isDeliveryBoyLoggedIn($salesChannelContext->getContext())) {
            return new RedirectResponse(
                $this->router->generate('delivery.boy.login.form')
            );
        }

        $travelProfile = $request->query->get('travelProfile');

        if ($travelProfile === null) {
            throw new \RuntimeException('travelProfile not given (possible values are: '. implode(', ', self::TRAVEL_PROFILES) . ')');
        }

        if (!\in_array($travelProfile, self::TRAVEL_PROFILES, true)) {
            throw new \RuntimeException('travelProfile is not valid, choose one of the following values: '. implode(', ', self::TRAVEL_PROFILES));
        }

        $deliveryBoyId = $this->deliveryBoyLoginService->getDeliveryBoyId();
        $result = $this->deliveryRouteService->generateRoute($deliveryBoyId, $travelProfile, $salesChannelContext->getContext());
        return new JsonResponse($result);
    }
}
