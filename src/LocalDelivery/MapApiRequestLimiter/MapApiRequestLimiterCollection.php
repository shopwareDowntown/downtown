<?php


namespace Shopware\Production\LocalDelivery\MapApiRequestLimiter;


use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                   add(MapApiRequestLimiterEntity $entity)
 * @method void                   set(string $key, MapApiRequestLimiterEntity $entity)
 * @method MapApiRequestLimiterEntity[]    getIterator()
 * @method MapApiRequestLimiterEntity[]    getElements()
 * @method MapApiRequestLimiterEntity|null get(string $key)
 * @method MapApiRequestLimiterEntity|null first()
 * @method MapApiRequestLimiterEntity|null last()
 */
class MapApiRequestLimiterCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MapApiRequestLimiterEntity::class;
    }
}
