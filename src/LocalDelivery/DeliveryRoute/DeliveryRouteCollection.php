<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryRoute;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                   add(DeliveryRouteEntity $entity)
 * @method void                   set(string $key, DeliveryRouteEntity $entity)
 * @method DeliveryRouteEntity[]    getIterator()
 * @method DeliveryRouteEntity[]    getElements()
 * @method DeliveryRouteEntity|null get(string $key)
 * @method DeliveryRouteEntity|null first()
 * @method DeliveryRouteEntity|null last()
 */
class DeliveryRouteCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DeliveryRouteEntity::class;
    }
}
