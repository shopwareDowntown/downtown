<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationAccessToken;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                               add(OrganizationAccessTokenEntity $entity)
 * @method void                               set(string $key, OrganizationAccessTokenEntity $entity)
 * @method OrganizationAccessTokenEntity[]    getIterator()
 * @method OrganizationAccessTokenEntity[]    getElements()
 * @method OrganizationAccessTokenEntity|null get(string $key)
 * @method OrganizationAccessTokenEntity|null first()
 * @method OrganizationAccessTokenEntity|null last()
 */
class OrganizationAccessTokenCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrganizationAccessTokenEntity::class;
    }
}
