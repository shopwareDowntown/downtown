<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationResetPasswordToken;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                      add(OrganizationResetPasswordTokenEntity $entity)
 * @method void                                      set(string $key, OrganizationResetPasswordTokenEntity $entity)
 * @method OrganizationResetPasswordTokenEntity[]    getIterator()
 * @method OrganizationResetPasswordTokenEntity[]    getElements()
 * @method OrganizationResetPasswordTokenEntity|null get(string $key)
 * @method OrganizationResetPasswordTokenEntity|null first()
 * @method OrganizationResetPasswordTokenEntity|null last()
 */
class OrganizationResetPasswordTokenCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrganizationResetPasswordTokenEntity::class;
    }
}
