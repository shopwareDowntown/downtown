<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Extensions;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtensionInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Organization\System\Organization\OrganizationDefinition;

class MediaEntityExtension implements EntityExtensionInterface
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('organizationMedia', OrganizationDefinition::class, 'logo_id')
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
