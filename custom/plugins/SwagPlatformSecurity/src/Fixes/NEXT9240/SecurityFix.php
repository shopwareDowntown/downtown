<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9240;

use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-9240';
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
            KernelEvents::RESPONSE => ['onResponse', 100],
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        if ($route !== 'administration.index') {
            return;
        }

        $nonce = base64_encode(random_bytes(8));

        $csp = implode(';', [
            "object-src 'none'",
            "script-src 'strict-dynamic' 'nonce-$nonce' 'unsafe-inline' 'unsafe-eval' https: http:",
            "base-uri 'self'",
        ]);

        $content = $event->getResponse()->getContent();
        $content = str_replace(
            ['<script src', '<script>'],
            [
                sprintf('<script nonce="%s" src', $nonce),
                sprintf('<script nonce="%s">', $nonce)
            ],
            $content
        );

        $event->getResponse()->setContent($content);
        $event->getResponse()->headers->set('Content-Security-Policy', $csp);
    }
}
