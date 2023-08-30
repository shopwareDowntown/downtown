<?php

namespace Shopware\Production\Locali;

use Shopware\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class LocaliBundle
 * @package Shopware\Production\Locali
 */
class LocaliBundle extends Bundle
{
    protected $name = 'LocaliBundle';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('services.xml');
        $this->registerMigrationPath($container);
    }
}
