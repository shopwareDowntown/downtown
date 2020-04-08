<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(OrganizationEntity $entity)
 * @method void                    set(string $key, OrganizationEntity $entity)
 * @method OrganizationEntity[]    getIterator()
 * @method OrganizationEntity[]    getElements()
 * @method OrganizationEntity|null get(string $key)
 * @method OrganizationEntity|null first()
 * @method OrganizationEntity|null last()
 */
class OrganizationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrganizationEntity::class;
    }
}
