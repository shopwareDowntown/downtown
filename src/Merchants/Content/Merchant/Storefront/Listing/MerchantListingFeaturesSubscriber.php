<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Storefront\Listing;

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
        $events = parent::getSubscribedEvents();
        $events[MerchantListingCriteriaEvent::class] = [
            ['handleListingRequest', 100],
            ['switchFilter', -100],
        ];

        $events[MerchantListingResultEvent::class] =  'handleResult';

        return $events;
    }

    public function handleListingRequest(ProductListingCriteriaEvent $event): void
    {
        parent::handleListingRequest($event);

        $criteria = $event->getCriteria();
        $this->removeFilter($criteria);
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
