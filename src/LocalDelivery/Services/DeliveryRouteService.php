<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Production\LocalDelivery\DeliveryPackage\DeliveryPackageCollection;
use Shopware\Production\LocalDelivery\DeliveryRoute\DeliveryRouteEntity;

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

    public function __construct(
        MapboxService $mapboxService,
        EntityRepositoryInterface $deliveryPackageRepository,
        EntityRepositoryInterface $deliveryRouteRepository
    )
    {
        $this->mapboxService = $mapboxService;
        $this->deliveryPackageRepository = $deliveryPackageRepository;
        $this->deliveryRouteRepository = $deliveryRouteRepository;
    }

    public function getNewestRoute(string $boyEntityId, Context $context): DeliveryRouteEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('deliveryBoyId', $boyEntityId));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));
        $criteria->setLimit(1);
        /** @var DeliveryRouteEntity $result */
        $result = $this->deliveryRouteRepository->search($criteria, $context)->getEntities()->first();

        if ($result === null) {
            throw new \RuntimeException('No route found. try to generate a new route for your packages.');
        }

        return $result;
    }

    public function generateRoute(string $boyEntityId, string $travelProfile, Context $context): DeliveryRouteEntity
    {
        // get packages that are not delivered for this boy
        $packages = $this->getNotDeliveredPackagesForBoy($boyEntityId, $context);
        $firstPackage = $packages->first();

        if ($firstPackage === null) {
            throw new \RuntimeException('No packages for route found');
        }

        // get merchant adress and coordinates
        $coordinates = [];
        $merchant = $firstPackage->getMerchant();
        if ($merchant === null) {
            throw new \RuntimeException('No merchant found for package');
        }

        $merchantCountry = $merchant->getCountry();
        if ($merchantCountry === null) {
            throw new \RuntimeException('No country for merchant provided');
        }

        $coordinates[] = $this->mapboxService->getGpsCoordinates(
            $this->mapboxService->convertAddressToSearchTerm(
                $merchant->getZip(),
                $merchant->getCity(),
                $merchant->getStreet(),
                $merchantCountry->getIso3()
            ),
            $context
        );

        // get coordinates from the package adresses
        foreach ($packages as $package) {
            $coordinates[] = $this->mapboxService->getGpsCoordinates(
                $this->mapboxService->convertAddressToSearchTerm(
                    $package->getRecipientZipcode(),
                    $package->getRecipientCity(),
                    $package->getRecipientStreet()
                ),
                $context
            );
        }

        // get optimized route from the coordinates
        $routeInformation = $this->mapboxService->getOptimizedRoute($coordinates, $travelProfile, $context);

        // save deliveryRoute
        $this->deliveryRouteRepository->create([
            [
                'deliveryBoyId' => $boyEntityId,
                'routeWaypoints' => $routeInformation
            ]
        ], $context);

        return $this->getNewestRoute($boyEntityId, $context);
    }

    private function getNotDeliveredPackagesForBoy(string $boyEntityId, Context $context): DeliveryPackageCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('deliveryBoyId', $boyEntityId));
        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [new EqualsFilter('status', 'delivered')])); // TODO: replace with delivered status string
        $criteria->addAssociation('merchant');
        /** @var DeliveryPackageCollection $entities */
        $entities = $this->deliveryPackageRepository->search($criteria, $context)->getEntities();

        return $entities;
    }
}
