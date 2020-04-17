<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\Content\Service\Aggregate\ServiceTranslation\ServiceTranslationDefinition;

class ServiceDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'service';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ServiceCollection::class;
    }

    public function getEntityClass(): string
    {
        return ServiceEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new TranslatedField('name')),
            new TranslationsAssociationField(ServiceTranslationDefinition::class, 'service_id')
        ]);
    }
}
