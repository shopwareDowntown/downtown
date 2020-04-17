<?php declare(strict_types=1);

namespace Shopware\Production\Merchants;

use Shopware\Core\Framework\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MerchantBundle extends Bundle
{
    protected $name = 'Merchant';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('api.xml');
        $loader->load('checkout.xml');
        $loader->load('commands.xml');
        $loader->load('merchant.xml');
        $loader->load('merchant_api.xml');
        $loader->load('merchant_listing.xml');
        $loader->load('storefront.xml');
        $loader->load('service.xml');
        $this->registerMigrationPath($container);
    }
}
