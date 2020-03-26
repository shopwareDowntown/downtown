<?php

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\SalesChannelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FixContextTokenMerchantApiSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['preStartSession', 50],
                ['postStartSession', 30],
            ]
        ];
    }

    public function preStartSession(RequestEvent $event): void
    {
        if (strpos($event->getRequest()->getPathInfo(), '/merchant-api/') !== 0) {
            return;
        }

        $event->getRequest()->attributes->set(SalesChannelRequest::ATTRIBUTE_IS_SALES_CHANNEL_REQUEST, false);
    }

    public function postStartSession(RequestEvent $event): void
    {
        if (strpos($event->getRequest()->getPathInfo(), '/merchant-api/') !== 0) {
            return;
        }

        $event->getRequest()->attributes->set(SalesChannelRequest::ATTRIBUTE_IS_SALES_CHANNEL_REQUEST, true);
    }
}
