<?php


namespace Shopware\Production\LocalDelivery\MapApiRequestLimiter;


use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class MapApiRequestLimiterDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'map_api_request_limiter';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MapApiRequestLimiterCollection::class;
    }

    public function getEntityClass(): string
    {
        return MapApiRequestLimiterEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('endpoint_name', 'endpointName'))->addFlags(new Required()),
            (new IntField('count', 'count')),
            (new IntField('limit', 'limit'))->addFlags(new Required())
        ]);
    }
}
