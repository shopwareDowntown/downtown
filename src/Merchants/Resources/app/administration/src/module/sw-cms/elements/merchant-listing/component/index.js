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
