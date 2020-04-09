<?php declare(strict_types=1);

namespace Shopware\Production\Organization;

class OrganizationEvents
{
    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    public const ORGANIZATION_WRITTEN_EVENT = 'organization.written';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    public const ORGANIZATION_DELETED_EVENT = 'organization.deleted';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    public const ORGANIZATION_LOADED_EVENT = 'organization.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    public const ORGANIZATION_SEARCH_RESULT_LOADED_EVENT = 'organization.search.result.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    public const ORGANIZATION_AGGREGATION_LOADED_EVENT = 'organization.aggregation.result.loaded';

    /**
     * @Event("Shopware\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    public const ORGANIZATION_ID_SEARCH_RESULT_LOADED_EVENT = 'organization.id.search.result.loaded';
}
