const { Module } = Shopware;

import './page/sw-settings-security-view';
import './extension/sw-settings-index';

Module.register('sw-settings-security', {
    type: 'plugin',
    name: 'settings-security',
    title: 'sw-settings-security.general.mainMenuItemGeneral',
    description: 'sw-settings-security.general.description',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'default-action-settings',
    favicon: 'icon-module-settings.png',

    routes: {
        index: {
            component: 'sw-settings-security-view',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index'
            }
        }
    },
});
