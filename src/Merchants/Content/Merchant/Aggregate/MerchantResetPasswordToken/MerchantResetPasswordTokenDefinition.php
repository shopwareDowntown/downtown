<?php

namespace Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantResetPasswordToken;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class MerchantResetPasswordTokenDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'merchant_reset_password_token';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('token', 'token'))->addFlags(new Required()),

            (new FkField('merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new Required()),
            (new ManyToOneAssociationField('merchant', 'merchant_id', MerchantDefinition::class))
        ]);
    }
}
