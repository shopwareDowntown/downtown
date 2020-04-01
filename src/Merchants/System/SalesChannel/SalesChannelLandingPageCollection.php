<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\System\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                         add(SalesChannelLandingPageEntity $entity)
 * @method void                                         set(string $key, SalesChannelLandingPageEntity $entity)
 * @method \Generator<SalesChannelLandingPageEntity>    getIterator()
 * @method SalesChannelLandingPageEntity[]              getElements()
 * @method SalesChannelLandingPageEntity|null           get(string $key)
 * @method SalesChannelLandingPageEntity|null           first()
 * @method SalesChannelLandingPageEntity|null           last()
 */
class SalesChannelLandingPageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SalesChannelLandingPageEntity::class;
    }
}
