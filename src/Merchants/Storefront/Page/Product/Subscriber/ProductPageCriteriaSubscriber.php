<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Page\Product\Subscriber;

use Shopware\Production\Merchants\Content\Merchant\MerchantAvailableFilter;
use Shopware\Storefront\Page\Product\ProductLoaderCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageCriteriaSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ProductLoaderCriteriaEvent::class => 'onProductCriteriaLoaded'
        ];
    }

    public function onProductCriteriaLoaded(ProductLoaderCriteriaEvent $event): void
    {
        $event->getCriteria()
            ->addAssociation('merchants')
            ->addFilter(new MerchantAvailableFilter(
                $event->getSalesChannelContext()->getSalesChannel()->getId(), 'merchants'
            ));
    }
}
