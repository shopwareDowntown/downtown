<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationResetPasswordToken;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Organization\System\Organization\OrganizationDefinition;

class OrganizationResetPasswordTokenDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'organization_reset_password';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return OrganizationResetPasswordTokenCollection::class;
    }

    public function getEntityClass(): string
    {
        return OrganizationResetPasswordTokenEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('token', 'token'))->addFlags(new Required()),

            (new FkField('organization_id', 'organizationId', OrganizationDefinition::class))->addFlags(new Required()),
            (new ManyToOneAssociationField('organization', 'organization_id', OrganizationDefinition::class))
        ]);
    }
}
