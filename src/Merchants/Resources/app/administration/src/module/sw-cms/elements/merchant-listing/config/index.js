import template from './sw-cms-el-config-merchant-listing.html.twig';
import './sw-cms-el-config-merchant-listing.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-merchant-listing', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('merchant-listing');
        }
    }
});
