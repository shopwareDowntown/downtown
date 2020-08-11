<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Extensions;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantMedia\MerchantMediaDefinition;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class MediaEntityExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new ManyToManyAssociationField('merchantMedia', MerchantDefinition::class, MerchantMediaDefinition::class, 'media_id', 'merchant_id')
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
