<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\EmailField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Production\Organization\System\Organization\Aggregate\OrganizationAccessToken\OrganizationAccessTokenDefinition;
use Shopware\Production\Organization\System\Organization\Aggregate\OrganizationDisclaimer\OrganizationDisclaimerDefinition;
use Shopware\Production\Organization\System\Organization\Aggregate\OrganizationResetPasswordToken\OrganizationResetPasswordTokenDefinition;

class OrganizationDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'organization';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return OrganizationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return OrganizationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new EmailField('email', 'email'))->addFlags(new Required()),
            (new PasswordField('password', 'password'))->addFlags(new Required()),

            (new StringField('first_name', 'firstName'))->addFlags(new Required()),
            (new StringField('last_name', 'lastName'))->addFlags(new Required()),

            (new StringField('phone', 'phone')),
            (new StringField('city', 'city')),
            (new StringField('post_code', 'postCode')),

            (new LongTextField('imprint', 'imprint')),
            (new LongTextField('tos', 'tos')),
            (new LongTextField('privacy', 'privacy')),

            (new FkField('logo_id', 'logoId', MediaDefinition::class)),
            (new OneToOneAssociationField('logo', 'logo_id', 'id', MediaDefinition::class, true)),

            // Home fields
            (new FkField('home_hero_image_id', 'homeHeroImageId', MediaDefinition::class))->addFlags(new CascadeDelete()),
            (new OneToOneAssociationField('homeHeroImage', 'home_hero_image_id', 'id', MediaDefinition::class, true)),

            (new LongTextField('home_text', 'homeText')),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            (new OneToOneAssociationField('salesChannel', 'sales_channel_id', 'id', SalesChannelDefinition::class, false)),

            (new OneToOneAssociationField('disclaimer', 'id', 'organization_id', OrganizationDisclaimerDefinition::class, true)),

            (new OneToManyAssociationField('accessTokens', OrganizationAccessTokenDefinition::class, 'organization_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('resetPasswordTokens', OrganizationResetPasswordTokenDefinition::class, 'organization_id'))->addFlags(new CascadeDelete()),
        ]);
    }
}
