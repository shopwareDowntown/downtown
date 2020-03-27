<?php


namespace Shopware\Production\LocalDelivery\DeliveryRoute\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class DeliveryRouteService
{
    /**
     * @var MapboxService
     */
    private $mapboxService;
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryPackageRepository;
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryRouteRepository;

    public function __construct(MapboxService $mapboxService, EntityRepositoryInterface $deliveryPackageRepository, EntityRepositoryInterface $deliveryRouteRepository)
    {
        $this->mapboxService = $mapboxService;
        $this->deliveryPackageRepository = $deliveryPackageRepository;
        $this->deliveryRouteRepository = $deliveryRouteRepository;
    }

    public function getNewestRoute(Context $context) {
//        $criteria = new Criteria();
//        $result = $this->deliveryPackageRepository->search($criteria, $context);
//        dd($result);

        return $this->mapboxService->getGpsCoordinates('adress', $context);
    }

}
