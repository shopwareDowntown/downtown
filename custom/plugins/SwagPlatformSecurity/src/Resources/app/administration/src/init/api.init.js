import swagSecurityApiClient from '../service/swagSecurityApiClient';
import swagSecurityState from '../service/swagSecurityState';
import { CookieStorage } from 'cookie-storage';

const { Application } = Shopware;

Application.addServiceProvider('swagSecurityApi', (container) => {
    const initContainer = Application.getContainer('init');
    return new swagSecurityApiClient(initContainer.httpClient, container.loginService);
});

Application.addServiceProvider('swagSecurityState', () => {
    return new swagSecurityState();
});

Application.addServiceProvider('swagSecurityCookieStorage', () => {
    const domain = Shopware.Context.api.host;
    const path = Shopware.Context.api.basePath + Shopware.Context.api.pathInfo;

    // Set default cookie values
    return new CookieStorage(
        {
            path: path,
            domain: domain,
            secure: location.protocol === 'https:',
            sameSite: 'Strict' // Should be Strict
        }
    );
})
