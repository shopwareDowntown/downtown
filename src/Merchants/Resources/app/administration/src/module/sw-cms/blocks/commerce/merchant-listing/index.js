import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'merchant-listing',
    label: 'sw-cms.blocks.commerce.merchantListing.label',
    category: 'commerce',
    hidden: true,
    removable: false,
    component: 'sw-cms-block-merchant-listing',
    previewComponent: 'sw-cms-preview-merchant-listing',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        content: 'merchant-listing'
    }
});
