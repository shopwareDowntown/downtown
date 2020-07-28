<?php declare(strict_types=1);

namespace Swag\Security\Components;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveDisabledServicesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $services = $container->findTaggedServiceIds('swag.security.fix');
        $activeFixes = $container->getParameter('SwagPlatformSecurity.activeFixes');

        foreach ($services as $id => $tag) {
            $ticket = $tag[0]['ticket'];

            if (!in_array($ticket, $activeFixes, true)) {
                $container->removeDefinition($id);
            }
        }
    }
}
