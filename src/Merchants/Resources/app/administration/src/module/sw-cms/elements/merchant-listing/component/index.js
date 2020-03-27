import template from './sw-cms-el-merchant-listing.html.twig';
import './sw-cms-el-merchant-listing.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-merchant-listing', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            demoMerchantCount: 8
        };
    },

    computed: {
        demoMerchantElement() {
            return {
                config: {
                    boxLayout: {
                        source: 'static',
                        value: this.element.config.boxLayout.value
                    },
                    displayMode: {
                        source: 'static',
                        value: 'standard'
                    }
                },
                data: {
                    merchant: {
                        name: 'Sample Merchant',
                        email: 'sample@merchant.com',
                        website: 'https://sample.merchant.com',
                        description: `Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                    sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                    sed diam voluptua.`.trim(),
                        phone_number: '+49 00 1324 5678'
                    }
                }
            };
        },
    },

    created() {
        this.createdComponent();
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('merchant-listing');
        },

        mountedComponent() {
            const section = this.$el.closest('.sw-cms-section');

            if (section.classList.contains('is--sidebar')) {
                this.demoMerchantCount = 6;
            }
        }
    }
});
