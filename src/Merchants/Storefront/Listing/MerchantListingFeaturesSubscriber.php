<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Listing;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingFeaturesSubscriber;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class MerchantListingFeaturesSubscriber extends ProductListingFeaturesSubscriber
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MerchantListingCriteriaEvent::class =>  [
                ['handleMerchantListingRequest', 100],
            ],
            MerchantListingResultEvent::class => 'handleResult'
        ];
    }

    public function handleMerchantListingRequest(ProductListingCriteriaEvent $event): void
    {
        $this->handleListingRequest($event);

        if (!$event instanceof MerchantListingCriteriaEvent) {
            return;
        }

        $criteria = $event->getCriteria();
        $this->removeFilter($criteria);
        $criteria->resetSorting();
        $criteria->resetGroupFields();
        $criteria->resetAggregations();
    }

    private function removeFilter(Criteria $criteria): void
    {
        $filters = $criteria->getFilters();

        $criteria->resetFilters();
        array_pop($filters);

        $criteria->addFilter(...$filters);
    }
}
