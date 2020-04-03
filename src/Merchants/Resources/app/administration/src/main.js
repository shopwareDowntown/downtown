import './module/sw-cms';
import './module/sw-sales-channel/component/sw-sales-channel-modal';
import './module/sw-sales-channel/view/sw-sales-channel-detail-base';
import './module/sw-sales-channel/view/sw-sales-channel-create-base';
import './module/sw-sales-channel/page/sw-sales-channel-detail';
import './module/sw-sales-channel/page/sw-sales-channel-create';
import './module/sw-sales-channel/component/structure/sw-admin-menu-extension';
import './module/sw-sales-channel-list';

import './app/component/structure/sw-search-bar';
import './app/component/structure/sw-page';

import cmsDe from './module/sw-cms/snippet/de-DE.json';
import cmsGb from './module/sw-cms/snippet/en-GB.json';

import searchDe from './app/component/snippet/de-DE.json';
import searchGb from './app/component/snippet/en-GB.json';

import salesChannelDe from './module/sw-sales-channel-list/snippet/de-DE.json';
import salesChannelGb from './module/sw-sales-channel-list/snippet/en-GB.json';

const { Locale } = Shopware;

Locale.extend('de-DE', cmsDe);
Locale.extend('en-GB', cmsGb);

Locale.extend('de-DE', searchDe);
Locale.extend('en-GB', searchGb);

Locale.extend('de-DE', salesChannelDe);
Locale.extend('en-GB', salesChannelGb);
