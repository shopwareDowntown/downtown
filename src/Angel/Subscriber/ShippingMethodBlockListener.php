<?php declare(strict_types=1);

namespace Shopware\Production\Angel\Subscriber;

use Shopware\Production\Merchants\Events\BlockShippingMethodsEvent;

class ShippingMethodBlockListener
{
    public function __invoke(BlockShippingMethodsEvent $event)
    {
        foreach ($event->getShippingMethodCollection() as $key => $shippingMethod) {
            if ($shippingMethod->getTranslation('name') === 'Angel') {
                /**
                 * This code is executed on an load of the page. When you do heavy things, please cache it :)
                 *
                 * Delivery address: $event->getContext()->getShippingLocation()->getAddress()
                 * Merchant information => $event->getMerchant()
                 */

                // Remove it with following line
//                $event->getShippingMethodCollection()->remove($key);
            }
        }
    }
}
