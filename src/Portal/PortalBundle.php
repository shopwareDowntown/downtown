<?php declare(strict_types=1);

namespace Shopware\Production\Portal;

use Shopware\Core\Framework\Bundle;
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
    }

    public function getThemeConfigPath(): string
    {
        return 'theme.json';
    }
}
