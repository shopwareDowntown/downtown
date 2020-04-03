import template from './sw-sales-channel-list-overview.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('sw-sales-channel-list-overview', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            salesChannels: null,
            repository: null,
            showModal: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        salesChannelRepository() {
            this.repository = this.repositoryFactory.create('sales_channel');
            return this.repository;
        },

        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: 'sw-sales-channel-list.grid.columns.name',
                routerLink: 'sw.sales.channel.detail.base',
                allowResize: true,
                primary: true
            }, {
                property: 'domains',
                label: 'sw-sales-channel-list.grid.columns.domain',
                allowResize: true,
                sortable: false,
            }, {
                property: 'active',
                dataIndex: 'active',
                label: 'sw-sales-channel-list.grid.columns.active',
                allowResize: true,
            }];
        }
    },

    methods: {
        getList() {
            this.loadEntityData();
        },

        loadEntityData() {
            const criteria = new Criteria(this.page, this.limit);

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort('sales_channel.name', 'ASC'));
            criteria.addAssociation('type');
            criteria.addAssociation('domains');

            this.salesChannelRepository.search(criteria, Shopware.Context.api).then((response) => {
                this.total = response.total;
                this.salesChannels = response;
            });
        },
    }
});
