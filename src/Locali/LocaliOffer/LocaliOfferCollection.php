<?php declare(strict_types=1);

namespace Shopware\Production\Locali\LocaliOffer;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                         add(LocaliOfferEntity $entity)
 * @method void                                         set(string $key, LocaliOfferEntity $entity)
 * @method \Generator<LocaliOfferEntity>    getIterator()
 * @method LocaliOfferEntity[]              getElements()
 * @method LocaliOfferEntity|null           get(string $key)
 * @method LocaliOfferEntity|null           first()
 * @method LocaliOfferEntity|null           last()
 */
class LocaliOfferCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LocaliOfferEntity::class;
    }
}
