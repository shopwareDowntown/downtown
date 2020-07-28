let cookieStorage = Shopware.Service('swagSecurityCookieStorage');
let swagSecurityState = Shopware.Service('swagSecurityState');

if (swagSecurityState.isActive('NEXT-9241')) {
    Shopware.Service('loginService').addOnLoginListener(() => {
        if (cookieStorage.getItem('bearerAuth')) {
            cookieStorage.setItem('bearerAuth', cookieStorage.getItem('bearerAuth'));
        }
    })

    Shopware.Service('loginService').addOnTokenChangedListener(() => {
        if (cookieStorage.getItem('bearerAuth')) {
            cookieStorage.setItem('bearerAuth', cookieStorage.getItem('bearerAuth'));
        }

    });
}
