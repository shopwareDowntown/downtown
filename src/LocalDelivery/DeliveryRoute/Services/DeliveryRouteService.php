<?php


namespace Shopware\Production\LocalDelivery\DeliveryRoute\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Production\LocalDelivery\DeliveryPackage\DeliveryPackageCollection;
use Shopware\Production\LocalDelivery\DeliveryRoute\DeliveryRouteEntity;
use Shopware\Production\Merchants\Content\Merchant\MerchantCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

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

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(
        MapboxService $mapboxService,
        EntityRepositoryInterface $deliveryPackageRepository,
        EntityRepositoryInterface $deliveryRouteRepository,
        EntityRepositoryInterface $merchantRepository
    )
    {
        $this->mapboxService = $mapboxService;
        $this->deliveryPackageRepository = $deliveryPackageRepository;
        $this->deliveryRouteRepository = $deliveryRouteRepository;
        $this->merchantRepository = $merchantRepository;
    }

    public function getNewestRoute(string $boyEntityId, Context $context): DeliveryRouteEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('deliveryBoyId', $boyEntityId));
        $criteria->addSorting([new FieldSorting('createdAt', FieldSorting::DESCENDING)]);
        $criteria->setLimit(1);
        /** @var DeliveryRouteEntity $result */
        $result = $this->deliveryRouteRepository->search($criteria, $context)->getEntities()->first();

        return $result;
    }

    public function generateRoute(string $boyEntityId, string $merchantId, string $travelProfile, Context $context): DeliveryRouteEntity
    {
        // get packages that are not delivered for this boy
        $packages = $this->getNotDeliveredPackagesForBoy($boyEntityId, $context);

        // get merchant adress and coordinates
        $coordinates = [];
        $merchant = $this->getMerchant($merchantId, $context);
        $coordinates[] = $this->mapboxService->getGpsCoordinates(
            $this->mapboxService->convertAddressToSearchTerm(
                $merchant->getZip(),
                $merchant->getCity(),
                $merchant->getStreet(),
                $merchant->getCountry()
            )
        );

        // get coordinates from the package adresses
        foreach ($packages as $package) {
            $coordinates[] = $this->mapboxService->getGpsCoordinates(
                $this->mapboxService->convertAddressToSearchTerm(
                    $package->getRecipientZipcode(),
                    $package->getRecipientCity(),
                    $package->getRecipientStreet()
                )
            );
        }

        // get optimized route from the coordinates
        $optimizedCoordinates = $this->mapboxService->getOptimizedRoute($coordinates, $travelProfile, $context);

        // save deliveryRoute
        $this->deliveryRouteRepository->create([
            'deliveryBoyId' => $boyEntityId,
            'routeWaypoints' => $optimizedCoordinates
        ], $context);

        return $this->getNewestRoute($boyEntityId, $context);
    }

    private function getNotDeliveredPackagesForBoy(string $boyEntityId, Context $context): DeliveryPackageCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('deliveryBoyId', $boyEntityId));
        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [new EqualsFilter('status', 'delivered')])); // TODO: replace with delivered status string
        /** @var DeliveryPackageCollection $entities */
        $entities = $this->deliveryPackageRepository->search($criteria, $context)->getEntities();

        return $entities;
    }

    private function getMerchant(string $merchantId, Context $context): MerchantEntity
    {
        $criteria = new Criteria([$merchantId]);
        /** @var MerchantCollection $entities */
        $entities = $this->merchantRepository->search($criteria, $context)->getEntities();

        return $entities->first();
    }
}
