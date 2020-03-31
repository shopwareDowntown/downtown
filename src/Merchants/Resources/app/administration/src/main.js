import './module/sw-cms';
import './module/sw-sales-channel/view/sw-sales-channel-detail-base';
import './module/sw-sales-channel/view/sw-sales-channel-create-base';
import './module/sw-sales-channel/page/sw-sales-channel-detail';
import './module/sw-sales-channel/page/sw-sales-channel-create';

import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

const { Locale } = Shopware;

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);
