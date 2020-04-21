<?php declare(strict_types=1);

namespace Shopware\Production\Organization;

use Shopware\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class OrganizationBundle extends Bundle
{
    protected $name = 'Organization';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('api.xml');
        $loader->load('api_controller.xml');
        $loader->load('dal.xml');
        $loader->load('storefront.xml');
    }
}
