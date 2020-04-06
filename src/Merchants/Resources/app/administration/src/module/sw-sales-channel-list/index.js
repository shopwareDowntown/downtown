import './page/sw-sales-channel-list-overview';

const { Module } = Shopware;

Module.register('sw-sales-channel-list', {
    type: 'plugin',
    name: 'SalesChannelList',
    title: 'sw-sales-channel-list.grid.headline',
    description: 'SalesChannel List Module',
    color: '#14D7A5',
    icon: 'default-device-server',

    routes: {
        overview: {
            component: 'sw-sales-channel-list-overview',
            path: 'overview'
        },
    },

    navigation: [{
        label: 'sw-sales-channel-list.menu-label',
        color: '#14D7A5',
        path: 'sw.sales.channel.list.overview',
        icon: 'default-device-server',
        position: 70
    }]
});
