const cookieKey = 'swagSecurityDidSeen';

let checkForSecurityPluginUpdate = async () => {
    let cookieStorage = Shopware.Service('swagSecurityCookieStorage');

    if (cookieStorage.getItem(cookieKey) !== null) {
        return;
    }

    let updateInfo = await Shopware.Service('swagSecurityApi').getUpdate();
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1);

    cookieStorage.setItem(cookieKey, '1', {
        expires: tomorrow
    });

    if (!updateInfo.updateAvailable) {
        return;
    }

    let applicationRoot = Shopware.Application.getApplicationRoot();

    const cancelLabel =
        applicationRoot.$tc('global.default.cancel');
    const updateLabel =
        applicationRoot.$tc('global.notification-center.shopware-updates-listener.updateNow');

    const notification = {
        title: applicationRoot.$t('sw-settings-security.notification.title', updateInfo),
        message: applicationRoot.$t('sw-settings-security.notification.message', updateInfo),
        variant: 'info',
        growl: true,
        system: true,
        actions: [{
            label: updateLabel,
            route: { name: 'sw.plugin.index.updates' }
        }, {
            label: cancelLabel
        }],
        autoClose: false
    };

    applicationRoot.$store.dispatch(
        'notification/createNotification',
        notification
    );
};

setTimeout(checkForSecurityPluginUpdate, 1000);
