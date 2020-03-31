<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                          add(MerchantEntity $entity)
 * @method void                          set(string $key, MerchantEntity $entity)
 * @method \Generator<MerchantEntity>    getIterator()
 * @method MerchantEntity[]              getElements()
 * @method MerchantEntity|null           get(string $key)
 * @method MerchantEntity|null           first()
 * @method MerchantEntity|null           last()
 */
class MerchantCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MerchantEntity::class;
    }
}
