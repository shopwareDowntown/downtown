<?php declare(strict_types=1);

namespace Shopware\Production;

use ReflectionMethod;
use Shopware\Core\Kernel as ShopwareKernel;
use Shopware\Core\Profiling\Doctrine\DebugStack;
use Shopware\Production\Merchants\MerchantBundle;
use Shopware\Production\Organization\OrganizationBundle;
use Shopware\Production\Portal\PortalBundle;
use Shopware\Production\Voucher\VoucherBundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Kernel extends ShopwareKernel
{
    public const PLACEHOLDER_DATABASE_URL = 'mysql://_placeholder.test';

    public function __construct(
        string $environment,
        bool $debug,
        BundleInterface $pluginLoader,
        ?string $cacheId = null,
        ?string $version = self::SHOPWARE_FALLBACK_VERSION
    ) {
        $cacheId = $cacheId ?? $environment;
        parent::__construct($environment, $debug, $pluginLoader, $cacheId, $version);
    }

    protected function initializeDatabaseConnectionVariables(): void
    {
        $url = $_ENV['DATABASE_URL']
            ?? $_SERVER['DATABASE_URL']
            ?? getenv('DATABASE_URL');

        if (isset($_SERVER['INSTALL']) || $url === self::PLACEHOLDER_DATABASE_URL) {
            return;
        }

        if ($this->getEnvironment() === 'dev') {
            self::getConnection()->getConfiguration()->setSQLLogger(
                new DebugStack()
            );
        }

        $reflection = new ReflectionMethod(\Shopware\Core\Kernel::class, 'initializeDatabaseConnectionVariables');
        if (!$reflection->isPrivate()) {
            parent::initializeDatabaseConnectionVariables();
        }
    }

    public function registerBundles()
    {
        yield from parent::registerBundles();

        yield new MerchantBundle();
        yield new PortalBundle();
        yield new VoucherBundle();
        yield new OrganizationBundle();
//        yield new LocalDeliveryBundle();
    }
}
