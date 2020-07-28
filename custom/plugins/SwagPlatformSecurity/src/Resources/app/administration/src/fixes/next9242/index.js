import './sw-profile-index';
import './sw-settings-user-detail';
import './sw-settings-user-list';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

if (Shopware.Service('swagSecurityState').isActive('NEXT-9242')) {
    Shopware.Service('loginService').verifyUserByUsername = (user, pass) => {
        return Shopware.Application.getContainer('init').httpClient.post('/oauth/token', {
            grant_type: 'password',
            client_id: 'administration',
            scope: 'user-verified',
            username: user,
            password: pass
        }, {
            baseURL: Shopware.Context.api.apiPath
        }).then((response) => {
            return {
                access: response.data.access_token,
                expiry: response.data.expires_in,
                refresh: response.data.refresh_token
            };
        });
    }

    Shopware.Locale.extend('de-DE', deDE);
    Shopware.Locale.extend('en-GB', enGB);
}
