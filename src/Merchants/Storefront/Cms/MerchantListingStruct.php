<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Cms;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\Struct;

class MerchantListingStruct extends Struct
{
    /**
     * @var EntitySearchResult|null
     */
    protected $listing;

    /**
     * @var string
     */
    protected $navigationId;

    public function getListing(): ?EntitySearchResult
    {
        return $this->listing;
    }

    public function setListing(EntitySearchResult $listing): void
    {
        $this->listing = $listing;
    }

    public function getNavigationId(): string
    {
        return $this->navigationId;
    }

    public function setNavigationId(string $navigationId): void
    {
        $this->navigationId = $navigationId;
    }

}
