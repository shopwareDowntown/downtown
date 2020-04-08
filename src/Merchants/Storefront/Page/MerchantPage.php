<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Page;

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
        $seo->setAuthor($merchant->getPublicCompanyName());
        $seo->setMetaTitle($merchant->getPublicCompanyName());

        $this->metaInformation = $seo;
    }
}
