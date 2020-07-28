<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9242;

use Shopware\Core\Framework\Api\OAuth\Scope\UserVerifiedScope;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(UserVerifiedScope::class)) {
            $container->setDefinition(UserVerifiedScope::class, (new Definition(UserVerifiedScope::class))->addTag('shopware.oauth.scope'));
        }

        $container->getDefinition(\Shopware\Core\Framework\Api\OAuth\ScopeRepository::class)
            ->setClass(ScopeRepository::class);
    }
}
