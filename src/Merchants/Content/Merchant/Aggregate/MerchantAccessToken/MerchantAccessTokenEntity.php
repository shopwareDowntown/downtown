<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantAccessToken;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

class MerchantAccessTokenEntity extends Entity
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var MerchantEntity|null
     */
    protected $merchant;

    public function getToken(): string
    {
        return $this->token;
    }

    public function getMerchant(): ?MerchantEntity
    {
        return $this->merchant;
    }
}
