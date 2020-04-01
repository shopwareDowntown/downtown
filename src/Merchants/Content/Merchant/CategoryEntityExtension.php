<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CategoryEntityExtension implements EntityExtensionInterface
{
    public function getDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }
    /**
     * @inheritDoc
     */
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('merchants', MerchantDefinition::class, 'category_id')
        );
    }
}
