const { Component } = Shopware;

Component.override('sw-sales-channel-detail', {
    computed: {
        landingPageRepository() {
            return this.repositoryFactory.create('sales_channel_landing_page');
        },
        organizationRepository() {
            return this.repositoryFactory.create('organization');
        },
    },

    methods: {
        loadSalesChannel() {
            this.isLoading = true;

            this.salesChannelRepository
                .get(this.$route.params.id, Shopware.Context.api, this.getLoadSalesChannelCriteria())
                .then((entity) => {
                    this.salesChannel = entity;

                    if (!this.salesChannel.maintenanceIpWhitelist) {
                        this.salesChannel.maintenanceIpWhitelist = [];
                    }

                    if (!this.salesChannel.extensions.landingPage) {
                        const landingPage = this.landingPageRepository.create(this.context);
                        landingPage.salesChannelId = entity.id;
                        this.salesChannel.extensions.landingPage = landingPage;
                    }

                    if (!this.salesChannel.extensions.organization) {
                        const organization = this.organizationRepository.create(this.context);
                        organization.salesChannelId = entity.id;
                        this.salesChannel.extensions.organization = organization;
                    }

                    this.generateAccessUrl();

                    this.isLoading = false;
                });
        },

        getLoadSalesChannelCriteria() {
            let criteria = this.$super('getLoadSalesChannelCriteria');

            criteria.addAssociation('organization');

            return criteria;
        },

        onSave() {
            if (!this.salesChannel.extensions.landingPage.cmsPageId) {
                this.createNotificationError({
                    title: this.$tc('sw-sales-channel.detail.titleSaveError'),
                    message: 'Please specify a landingpage layout'
                });

                return;
            }

            this.$super('onSave');
        }
    }
});
