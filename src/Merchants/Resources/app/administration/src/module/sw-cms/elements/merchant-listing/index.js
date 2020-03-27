import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'merchant-listing',
    label: 'sw-cms.elements.merchantListing.label',
    hidden: true,
    removable: false,
    component: 'sw-cms-el-merchant-listing',
    previewComponent: 'sw-cms-el-preview-merchant-listing',
    configComponent: 'sw-cms-el-config-merchant-listing',
    defaultConfig: {
        boxLayout: {
            source: 'static',
            value: 'standard'
        }
    }
});
