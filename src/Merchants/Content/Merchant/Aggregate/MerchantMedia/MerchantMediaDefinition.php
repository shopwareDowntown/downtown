<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantMedia;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class MerchantMediaDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'merchant_media';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('merchant_id', 'merchantId', MerchantDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('merchant', 'merchant_id', MerchantDefinition::class),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class),
            new CreatedAtField(),
        ]);
    }
}
