<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Pagelet\Footer;

use Shopware\Storefront\Pagelet\Footer\FooterPageletLoadedEvent;

class FooterPageletSubscriber
{
    public function __invoke(FooterPageletLoadedEvent $event)
    {
        $portalUrl = \getenv('MERCHANT_PORTAL');
        if (!\is_string($portalUrl)) {
            return;
        }

        $portalStruct = new PortalStruct($portalUrl);
        $event->getPagelet()->addExtension('portal', $portalStruct);
    }
}
