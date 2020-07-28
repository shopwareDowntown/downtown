<?php

use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;
use Shopware\Core\Framework\Adapter\Cache\CacheIdLoader;
use Swag\Security\Api\ConfigController;
use Swag\Security\Api\SecurityController;
use Swag\Security\Components\State;
use Swag\Security\Subscriber\AdminSecurityFixesProvider;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(State::class)
            ->args([
                '%SwagPlatformSecurity.availableFixes%',
                '%SwagPlatformSecurity.activeFixes%'
            ])
        ->set(SecurityController::class)
            ->public()
            ->args([
                new Reference(State::class),
                new Reference('plugin.repository'),
                '%kernel.cache_dir%',
                new Definition(Client::class),
                new Reference(CacheIdLoader::class),
            ])
        ->set(ConfigController::class)
            ->public()
            ->args([
                new Reference(Connection::class),
                new Reference('user.repository'),
            ])
        ->set(AdminSecurityFixesProvider::class)
            ->public()
            ->args([
                new Reference(State::class),
            ])
            ->tag('kernel.event_listener');

    // Fixes
    $container->services()
        ->set(Swag\Security\Fixes\NEXT9241\SecurityFix::class)
            ->tag('kernel.event_subscriber')
            ->tag('swag.security.fix', ['ticket' => Swag\Security\Fixes\NEXT9241\SecurityFix::class])
        ->set(Swag\Security\Fixes\NEXT9240\SecurityFix::class)
            ->tag('kernel.event_subscriber')
            ->tag('swag.security.fix', ['ticket' => Swag\Security\Fixes\NEXT9240\SecurityFix::class])
        ->set(Swag\Security\Fixes\NEXT9242\SecurityFix::class)
            ->tag('kernel.event_subscriber')
            ->tag('swag.security.fix', ['ticket' => Swag\Security\Fixes\NEXT9242\SecurityFix::class])
        ->set(Swag\Security\Fixes\NEXT9243\SecurityFix::class)
            ->tag('kernel.event_subscriber')
            ->tag('swag.security.fix', ['ticket' => Swag\Security\Fixes\NEXT9243\SecurityFix::class])
        ->set(Swag\Security\Fixes\NEXT9569\SecurityFix::class)
            ->tag('kernel.event_subscriber')
            ->tag('swag.security.fix', ['ticket' => Swag\Security\Fixes\NEXT9569\SecurityFix::class]);
};
