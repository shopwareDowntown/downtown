<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9241;

use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-9241';
    }

    public static function getMinVersion(): string
    {
        return '6.0.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.2.2';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', PHP_INT_MAX],
            BeforeSendResponseEvent::class => ['onResponse', PHP_INT_MIN]
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->getRequest()->isSecure()) {
            return;
        }

        @ini_set('session.cookie_secure', '1');
    }

    public function onResponse(BeforeSendResponseEvent $event): void
    {
        if (!$event->getRequest()->isSecure()) {
            return;
        }

        $secure = \Closure::bind(function (Cookie $cookie) {
            $cookie->secure = true;
        }, null, Cookie::class);

        foreach ($event->getResponse()->headers->getCookies() as $cookie) {
            $secure($cookie);
        }
    }
}
