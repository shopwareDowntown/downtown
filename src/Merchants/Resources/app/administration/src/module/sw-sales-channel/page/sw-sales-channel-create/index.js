const { Component } = Shopware;

Component.override('sw-sales-channel-create', {
    computed: {
        organizationRepository() {
            return this.repositoryFactory.create('organization');
        }
    },

    methods: {
        createdComponent() {
            this.$super('createdComponent');

            this.$set(this.salesChannel.extensions, 'organization', this.organizationRepository.create(Shopware.Context.Api));
            this.salesChannel.extensions.organization.salesChannelId = this.salesChannel.id;

            // This will be later replaced by an entity event
            this.salesChannel.extensions.organization.password = '12345678';
        }
    }
});
