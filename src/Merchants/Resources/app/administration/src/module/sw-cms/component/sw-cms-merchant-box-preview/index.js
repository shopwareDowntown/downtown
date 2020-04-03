import template from './sw-cms-merchant-box-preview.html.twig';
import './sw-cms-merchant-box-preview.scss';

const { Component } = Shopware;

Component.register('sw-cms-merchant-box-preview', {
    template,

    props: {
        hasText: {
            type: Boolean,
            default: true,
            required: false
        }
    }
});
