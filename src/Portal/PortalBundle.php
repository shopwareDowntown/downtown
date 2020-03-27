<?php

namespace Shopware\Production\Portal;

use Shopware\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class PortalBundle extends Bundle
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
        $this->registerMigrationPath($container);
    }
}
