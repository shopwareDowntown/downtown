<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryRoute;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyDefinition;

class DeliveryRouteDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'delivery_route';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return DeliveryRouteCollection::class;
    }

    public function getEntityClass(): string
    {
        return DeliveryRouteEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new JsonField('route_waypoints', 'routeWaypoints'))->addFlags(new Required()),

            new FkField('delivery_boy_id', 'deliveryBoyId', DeliveryBoyDefinition::class),
            new ManyToOneAssociationField('deliveryBoy', 'delivery_boy_id', DeliveryBoyDefinition::class),
        ]);
    }
}
