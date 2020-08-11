<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Extensions;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Production\Organization\System\Organization\OrganizationDefinition;

class MediaEntityExtension extends EntityExtension
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
