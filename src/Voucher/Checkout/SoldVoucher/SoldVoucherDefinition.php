<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Checkout\SoldVoucher;

use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PriceDefinitionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;

class SoldVoucherDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'sold_voucher';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SoldVoucherCollection::class;
    }

    public function getEntityClass(): string
    {
        return SoldVoucherEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('code', 'code'))->addFlags(new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new PriceDefinitionField('value', 'value'),
            new FkField('merchant_id', 'merchantId', MerchantDefinition::class),
            new FkField('order_line_item_id', 'orderLineItemId', OrderLineItemDefinition::class),
            new DateTimeField('redeemed_at', 'redeemedAt'),
            (new ReferenceVersionField(OrderLineItemDefinition::class))->addFlags(new Required()),

            new ManyToOneAssociationField('orderLineItem', 'order_line_item_id', OrderLineItemDefinition::class, 'id', false),
            new ManyToOneAssociationField('merchant', 'merchant_id', MerchantDefinition::class, 'id', false),
        ]);
    }
}
