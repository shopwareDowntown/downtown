<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantResetPasswordToken;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                            add(MerchantResetPasswordTokenEntity $entity)
 * @method void                                            set(string $key, MerchantResetPasswordTokenEntity $entity)
 * @method \Generator<MerchantEntity>                      getIterator()
 * @method MerchantResetPasswordTokenEntity[]              getElements()
 * @method MerchantResetPasswordTokenEntity|null           get(string $key)
 * @method MerchantResetPasswordTokenEntity|null           first()
 * @method MerchantResetPasswordTokenEntity|null           last()
 */
class MerchantResetPasswordTokenCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MerchantResetPasswordTokenEntity::class;
    }
}
