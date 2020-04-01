<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\System\SalesChannel\Extensions;

use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Merchants\System\SalesChannel\SalesChannelLandingPageDefinition;

class CmsPageExtension implements EntityExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return CmsPageDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('salesChannelLandingPage', 'id', 'cms_page_id', SalesChannelLandingPageDefinition::class, false)
        );
    }
}
