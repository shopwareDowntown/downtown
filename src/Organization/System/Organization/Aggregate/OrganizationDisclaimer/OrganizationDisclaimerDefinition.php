<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationDisclaimer;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Organization\System\Organization\OrganizationDefinition;

class OrganizationDisclaimerDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'organization_disclaimer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return OrganizationDisclaimerEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),

            (new BoolField('active', 'active')),

            (new StringField('text', 'text')),
            (new FkField('image_id', 'imageId', MediaDefinition::class)),
            (new OneToOneAssociationField('image', 'image_id', 'id', MediaDefinition::class, true)),


            (new FkField('organization_id', 'organizationId', OrganizationDefinition::class))->addFlags(new Required()),
            (new OneToOneAssociationField('organization', 'organization_id', 'id', OrganizationDefinition::class, false))
        ]);
    }
}
