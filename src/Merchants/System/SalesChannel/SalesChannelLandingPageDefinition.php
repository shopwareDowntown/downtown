<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\System\SalesChannel;

use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class SalesChannelLandingPageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'sales_channel_landing_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SalesChannelLandingPageCollection::class;
    }

    public function getEntityClass(): string
    {
        return SalesChannelLandingPageEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            new OneToOneAssociationField('salesChannel', 'sales_channel_id', 'id', SalesChannelDefinition::class, false),

            (new FkField('cms_page_id', 'cmsPageId', CmsPageDefinition::class))->addFlags(new Required()),
            new OneToOneAssociationField('cmsPage', 'cms_page_id', 'id', CmsPageDefinition::class)
        ]);
    }
}
