<?php declare(strict_types=1);

namespace Shopware\Production\Portal;

use Shopware\Core\Framework\Bundle;
use Shopware\Core\Framework\DependencyInjection\CompilerPass\RouteScopeCompilerPass;
use Shopware\Storefront\Framework\ThemeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class PortalBundle extends Bundle implements ThemeInterface
{
    protected $name = 'PortalBundle';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('snippets.xml');
        $loader->load('hacks.xml');
        $loader->load('services.xml');
        $this->registerMigrationPath($container);

        $this->removeRouteScopePrefixCompilerPass($container);
    }

    public function getThemeConfigPath(): string
    {
        return 'theme.json';
    }

    /**
     * Shopware Clod introduced prefixes for route scopes. Unfortunately that kills dependency injection of the route scopes
     * @see https://github.com/shopware/platform/commit/2af081233fe8fcd78847ca2ea11a7137b74be047
     *
     * This method removes the core compiler pass
     */
    private function removeRouteScopePrefixCompilerPass(ContainerBuilder $container)
    {
        $config = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        foreach ($config as $key => $item) {
            if (get_class($item) === RouteScopeCompilerPass::class) {
                unset($config[$key]);
            }
        }

        $container->getCompilerPassConfig()->setBeforeOptimizationPasses($config);
        $container->setParameter('shopware.routing.registered_api_prefixes', [
            '_wdt',
            '_profiler',
            '_error',
            'api',
            'sales-channel-api',
            'store-api',
            'admin',
            'api',
            'merchant-api',
            'organization-api',
        ]);
    }
}
