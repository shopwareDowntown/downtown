<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantProduct\MerchantProductDefinition;

class ProductEntityExtension implements EntityExtensionInterface
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
