<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryPackage;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                   add(DeliveryPackageEntity $entity)
 * @method void                   set(string $key, DeliveryPackageEntity $entity)
 * @method DeliveryPackageEntity[]    getIterator()
 * @method DeliveryPackageEntity[]    getElements()
 * @method DeliveryPackageEntity|null get(string $key)
 * @method DeliveryPackageEntity|null first()
 * @method DeliveryPackageEntity|null last()
 */
class DeliveryPackageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DeliveryPackageEntity::class;
    }
}
