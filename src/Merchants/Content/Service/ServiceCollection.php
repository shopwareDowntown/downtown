<?php declare(strict_types=1);


namespace Shopware\Production\Merchants\Content\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(ServiceEntity $entity)
 * @method void               set(string $key, ServiceEntity $entity)
 * @method ServiceEntity[]    getIterator()
 * @method ServiceEntity[]    getElements()
 * @method ServiceEntity|null get(string $key)
 * @method ServiceEntity|null first()
 * @method ServiceEntity|null last()
 */
class ServiceCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ServiceEntity::class;
    }
}
