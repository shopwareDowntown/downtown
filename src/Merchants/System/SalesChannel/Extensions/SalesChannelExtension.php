<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\System\SalesChannel\Extensions;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Production\Merchants\System\SalesChannel\SalesChannelLandingPageDefinition;

class SalesChannelExtension implements EntityExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return SalesChannelDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('landingPage', 'id', 'sales_channel_id', SalesChannelLandingPageDefinition::class)
        );
    }
}
