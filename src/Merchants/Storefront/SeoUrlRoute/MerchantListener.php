<?php

namespace Shopware\Production\Merchants\Storefront\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Production\Merchants\MerchantEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MerchantListener implements EventSubscriberInterface
{
    /**
     * @var SeoUrlUpdater
     */
    private $seoUrlUpdater;

    public static function getSubscribedEvents(): array
    {
        return [
            MerchantEvents::MERCHANT_WRITTEN_EVENT => 'merchantWritten'
        ];
    }

    public function __construct(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public function merchantWritten(EntityWrittenEvent $event)
    {
        $this->seoUrlUpdater->update(MerchantPageSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}
