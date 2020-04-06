const { Component } = Shopware;

/**
 * Adds "SalesChannel" search label and option
 */
Component.override('sw-search-bar', {
    methods: {
        createdComponent() {
            this.$super('createdComponent');

            this.searchTypes.salesChannel = {
                entityName: 'salesChannel',
                entityService: 'salesChannelService',
                placeholderSnippet: 'sw-sales-channel-list.search-bar.placeholder',
                listingRoute: 'sw.sales.channel.list.overview'
            };
            this.typeSelectResults = Object.values(this.searchTypes);
        },
    }
});
