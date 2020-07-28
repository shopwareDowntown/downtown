<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9569;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\PlatformRequest;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-9569';
    }

    public static function getMinVersion(): string
    {
        return '6.1.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.2.2';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse', -10]
        ];
    }

    private const HEADERS = [
        PlatformRequest::HEADER_VERSION_ID,
        PlatformRequest::HEADER_LANGUAGE_ID,
        PlatformRequest::HEADER_CONTEXT_TOKEN,
    ];

    public function onResponse(ResponseEvent $event): void
    {
        /** @var RouteScope|null $routeScope */
        $routeScope = $event->getRequest()->attributes->get('_routeScope');

        if ($routeScope === null || !$routeScope->hasScope('storefront')) {
            return;
        }

        foreach (self::HEADERS as $headerKey) {
            $event->getResponse()->headers->remove($headerKey);
        }
    }
}
