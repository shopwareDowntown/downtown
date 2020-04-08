const { Component } = Shopware;

/**
 * Adds "back to overview" button on sales channel detail page
 */
Component.override('sw-page', {
    methods: {
        initPage() {
            this.$super('initPage');

            if (this.$route.name === 'sw.sales.channel.detail.base') {
                this.parentRoute = 'sw.sales.channel.list.overview';
            }
        }
    }
});
