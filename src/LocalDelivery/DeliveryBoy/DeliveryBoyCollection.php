<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryBoy;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                   add(DeliveryBoyEntity $entity)
 * @method void                   set(string $key, DeliveryBoyEntity $entity)
 * @method DeliveryBoyEntity[]    getIterator()
 * @method DeliveryBoyEntity[]    getElements()
 * @method DeliveryBoyEntity|null get(string $key)
 * @method DeliveryBoyEntity|null first()
 * @method DeliveryBoyEntity|null last()
 */
class DeliveryBoyCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DeliveryBoyEntity::class;
    }
}
