<?php declare(strict_types=1);

namespace Shopware\Production\Merchants;

class MerchantEvents
{
    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    public const MERCHANT_WRITTEN_EVENT = 'merchant.written';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    public const MERCHANT_DELETED_EVENT = 'merchant.deleted';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    public const MERCHANT_LOADED_EVENT = 'merchant.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    public const MERCHANT_SEARCH_RESULT_LOADED_EVENT = 'merchant.search.result.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    public const MERCHANT_AGGREGATION_LOADED_EVENT = 'merchant.aggregation.result.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    public const MERCHANT_ID_SEARCH_RESULT_LOADED_EVENT = 'merchant.id.search.result.loaded';
}
