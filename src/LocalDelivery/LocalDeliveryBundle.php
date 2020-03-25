<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery;


use Shopware\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class LocalDeliveryBundle extends Bundle
{
    protected $name = 'LocalDelivery';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('localDelivery.xml');
        $this->registerMigrationPath($container);
    }
}
