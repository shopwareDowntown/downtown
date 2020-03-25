<?php

namespace Shopware\Production\Merchants\Content\Merchant\Storefront\Page;

use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Page;

class MerchantPage extends Page
{
    /**
     * @var MerchantEntity
     */
    protected $merchant;

    public function getMerchant(): MerchantEntity
    {
        return $this->merchant;
    }

    public function setMerchant(MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
        $seo = new MetaInformation();
        $seo->setAuthor($merchant->getName());
        $seo->setMetaTitle($merchant->getName());

        $this->metaInformation = $seo;
    }
}
