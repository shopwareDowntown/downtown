<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9242;

use Shopware\Core\Framework\Api\OAuth\Scope\UserVerifiedScope;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserDefinition;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityFix extends AbstractSecurityFix
{
    private const API_ROUTES = [
        'api.user.delete',
        'api.user.create',
        'api.user.update',
    ];

    public static function getTicket(): string
    {
        return 'NEXT-9242';
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
            PreWriteValidationEvent::class => 'onPreValidate',
            KernelEvents::CONTROLLER => ['onValidateApiRequest', -100],
            KernelEvents::RESPONSE => 'removeRefreshTokenOnVerifiedScope'
        ];
    }

    public static function buildContainer(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CompilerPass());
    }

    public function onPreValidate(PreWriteValidationEvent $event): void
    {
        foreach ($event->getCommands() as $command) {
            // @codeCoverageIgnoreStart
            if (!$command->isValid()) {
                continue;
            }
            // @codeCoverageIgnoreEnd

            if (
                $command->getDefinition() instanceof UserDefinition &&
                $event->getContext()->getScope() !== Context::SYSTEM_SCOPE &&
                !$event->getContext()->hasExtension('swagSecurityValidatedRequest')
            ) {
                throw new AccessDeniedHttpException(
                    sprintf(
                        'Write access to entity "%s" are not allowed in scope "%s".',
                        $command->getDefinition()->getEntityName(),
                        $event->getContext()->getScope()
                    )
                );
            }
        }
    }

    public function onValidateApiRequest(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array($request->attributes->get('_route'), self::API_ROUTES, true)) {
            return;
        }

        if (!$this->hasScope($request, UserVerifiedScope::IDENTIFIER)) {
            throw new AccessDeniedHttpException(sprintf('This access token does not have the scope "%s" to process this Request', UserVerifiedScope::IDENTIFIER));
        }

        /** @var Context $context */
        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
        $context->addExtension('swagSecurityValidatedRequest', new ArrayEntity());
    }

    public function removeRefreshTokenOnVerifiedScope(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->attributes->get('_route') !== 'api.oauth.token') {
            return;
        }

        $scopes = $request->get('scope', []);
        if (is_string($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        if (!in_array(UserVerifiedScope::IDENTIFIER, $scopes, true)) {
            return;
        }

        $body = json_decode($response->getContent(), true);

        if (isset($body['refresh_token'])) {
            unset($body['refresh_token']);

            $response->setContent(json_encode($body));
        }
    }

    private function hasScope(Request $request, string $scopeIdentifier): bool
    {
        $scopes = array_flip($request->attributes->get(PlatformRequest::ATTRIBUTE_OAUTH_SCOPES));

        return isset($scopes[$scopeIdentifier]);
    }
}
