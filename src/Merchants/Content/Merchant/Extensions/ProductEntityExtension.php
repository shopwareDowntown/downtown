<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Extensions;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantProduct\MerchantProductDefinition;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class ProductEntityExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    /**
     * @inheritDoc
     */
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new ManyToManyAssociationField('merchants', MerchantDefinition::class, MerchantProductDefinition::class, 'product_id', 'merchant_id')
        );
    }
}
