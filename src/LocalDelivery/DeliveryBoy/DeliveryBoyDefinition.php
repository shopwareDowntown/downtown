<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryBoy;

use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ReadProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\LocalDelivery\DeliveryPackage\DeliveryPackageDefinition;

class DeliveryBoyDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'delivery_boy';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return DeliveryBoyCollection::class;
    }

    public function getEntityClass(): string
    {
        return DeliveryBoyEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('title', 'title'),
            (new StringField('first_name', 'firstName'))->addFlags(new Required()),
            (new StringField('last_name', 'lastName'))->addFlags(new Required()),
            (new PasswordField('password', 'password'))->addFlags(new ReadProtected(SalesChannelApiSource::class, AdminApiSource::class)),
            (new StringField('email', 'email'))->addFlags(new Required()),
            new StringField('session_id', 'sessionId'),
            new BoolField('active', 'active'),
            (new StringField('zipcode', 'zipcode'))->addFlags(new Required()),
            (new StringField('city', 'city'))->addFlags(new Required()),
            (new StringField('street', 'street'))->addFlags(new Required()),
            (new StringField('phone_number', 'phoneNumber'))->addFlags(new Required()),

            new OneToManyAssociationField('deliveryPackages', DeliveryPackageDefinition::class, 'delivery_package_id'),
        ]);
    }
}
