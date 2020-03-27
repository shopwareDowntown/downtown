import './module/sw-cms/elements/merchant-listing';
import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

const { Locale } = Shopware;

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);
