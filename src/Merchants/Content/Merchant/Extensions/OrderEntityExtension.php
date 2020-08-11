<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Extensions;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantOrder\MerchantOrderDefinition;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class OrderEntityExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
    /**
     * @inheritDoc
     */
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new ManyToManyAssociationField('merchants', MerchantDefinition::class, MerchantOrderDefinition::class, 'order_id', 'merchant_id')
        );
    }
}
