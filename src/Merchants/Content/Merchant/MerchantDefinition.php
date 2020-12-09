<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Shipping\ShippingMethodDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ReadProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Country\CountryDefinition;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantAccessToken\MerchantAccessTokenDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantMedia\MerchantMediaDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantOrder\MerchantOrderDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantProduct\MerchantProductDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantResetPasswordToken\MerchantResetPasswordTokenDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantService\MerchantServiceDefinition;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantShippingMethod\MerchantShippingMethodDefinition;
use Shopware\Production\Merchants\Content\Service\ServiceDefinition;
use Shopware\Production\Voucher\Checkout\SoldVoucher\SoldVoucherDefinition;

class MerchantDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'merchant';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MerchantCollection::class;
    }

    public function getEntityClass(): string
    {
        return MerchantEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new BoolField('active', 'active')),

            // public profile fields
            (new BoolField('public', 'public')),
            (new StringField('public_company_name', 'publicCompanyName'))->addFlags(new Required()),
            (new StringField('public_owner', 'publicOwner'))->addFlags(),
            (new StringField('public_phone_number', 'publicPhoneNumber')),
            (new StringField('public_email', 'publicEmail'))->addFlags(),
            (new LongTextField('public_opening_times', 'publicOpeningTimes'))->addFlags(),
            (new LongTextField('public_description', 'publicDescription'))->addFlags(),
            new StringField('public_website', 'publicWebsite'),
            (new IntField('availability', 'availability', 0, 2)),
            new StringField('availability_text', 'availabilityText'),

            (new FkField('category_id', 'categoryId', CategoryDefinition::class)),
            (new OneToOneAssociationField('category', 'category_id', 'id', CategoryDefinition::class, false)),

            // account fields
            (new StringField('first_name', 'firstName'))->addFlags(),
            (new StringField('last_name', 'lastName'))->addFlags(),
            (new StringField('street', 'street'))->addFlags(),
            (new StringField('zip', 'zip'))->addFlags(),
            (new StringField('city', 'city'))->addFlags(),

            (new FkField('country_id', 'countryId', CountryDefinition::class))->addFlags(),
            (new OneToOneAssociationField('country', 'country_id', 'id', CountryDefinition::class)),

            (new StringField('email', 'email'))->addFlags(new Required()),
            (new PasswordField('password', 'password'))->addFlags(new Required(), new ReadProtected(SalesChannelApiSource::class)),
            (new StringField('phone_number', 'phoneNumber')),

            (new LongTextField('imprint', 'imprint')),
            (new LongTextField('tos', 'tos')),
            (new LongTextField('privacy', 'privacy')),
            (new LongTextField('revocation', 'revocation')),


            (new StringField('mollie_prod_key', 'mollieProdKey')),
            (new StringField('mollie_test_key', 'mollieTestKey')),
            (new BoolField('mollie_test_enabled', 'mollieTestEnabled')),

            (new StringField('payment_methods', 'paymentMethods')),


            // internal model fields
            (new StringField('activation_code', 'activationCode')),
            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            (new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class)),

            (new FkField('cover_id', 'coverId', MediaDefinition::class)),
            (new OneToOneAssociationField('cover', 'cover_id', 'id', MediaDefinition::class, false))->addFlags(new CascadeDelete()),

            (new OneToManyAssociationField('resetPasswordTokens', MerchantResetPasswordTokenDefinition::class, 'merchant_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('accessTokens', MerchantAccessTokenDefinition::class, 'merchant_id'))->addFlags(new CascadeDelete()),

            new ManyToManyAssociationField('products', ProductDefinition::class, MerchantProductDefinition::class, 'merchant_id', 'product_id'),
            new ManyToManyAssociationField('orders', OrderDefinition::class, MerchantOrderDefinition::class, 'merchant_id', 'order_id'),
            new ManyToManyAssociationField('media', MediaDefinition::class, MerchantMediaDefinition::class, 'merchant_id', 'media_id'),
            new ManyToManyAssociationField('shippingMethods', ShippingMethodDefinition::class, MerchantShippingMethodDefinition::class, 'merchant_id', 'shipping_method_id'),
            new ManyToManyAssociationField('services', ServiceDefinition::class, MerchantServiceDefinition::class, 'merchant_id', 'service_id'),
            new OneToManyAssociationField('soldVouchers', SoldVoucherDefinition::class, 'merchant_id')
        ]);
    }
}
