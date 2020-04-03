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

class DeliveryPackageService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryPackageRepository;

    public function __construct(EntityRepositoryInterface $deliveryPackageRepository)
    {
        $this->deliveryPackageRepository = $deliveryPackageRepository;
    }

    public function getPackagesByDeliveryBoyId(string $deliveryBoyId, Context $context): array
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('deliveryBoyId', $deliveryBoyId));

        return $this->deliveryPackageRepository->search($criteria, $context)->getElements();
    }
}
