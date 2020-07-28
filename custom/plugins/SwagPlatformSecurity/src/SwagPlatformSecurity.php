<?php declare(strict_types=1);

namespace Swag\Security;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Kernel;
use Swag\Security\Components\RemoveDisabledServicesCompilerPass;
use Swag\Security\Components\State;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SwagPlatformSecurity extends Plugin
{
    public const PLUGIN_NAME = 'SwagPlatformSecurity';

    public function build(ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator([__DIR__ . '/Resources/config']));
        $loader->load('services.php');

        $this->fetchPluginConfig($container);
        $container->addCompilerPass(new RemoveDisabledServicesCompilerPass());
    }

    private function fetchPluginConfig(ContainerBuilder $container): void
    {
        $qb = Kernel::getConnection()->createQueryBuilder();

        try {
            $config = $qb
                ->select(['ticket', 'active'])
                ->from('swag_security_config', 'config')
                ->execute()
                ->fetchAll(\PDO::FETCH_KEY_PAIR);

        } catch (TableNotFoundException $e) {
            $config = [];
        }

        foreach ($config as &$item) {
            $item = (bool) $item;
        }
        unset($item);

        $shopwareVersion = $container->getParameter('kernel.shopware_version');
        $availableFixes = [];
        $activeFixes = [];

        foreach (State::KNOWN_ISSUES as $knownIssue) {
            if (!$knownIssue::isValidForVersion($shopwareVersion)) {
                continue;
            }

            $availableFixes[] = $knownIssue;

            if (array_key_exists($knownIssue::getTicket(), $config) && !$config[$knownIssue::getTicket()]) {
                continue;
            }

            $knownIssue::buildContainer($container);

            $activeFixes[] = $knownIssue;
        }

        $container->setParameter('SwagPlatformSecurity.activeFixes', $activeFixes);
        $container->setParameter('SwagPlatformSecurity.availableFixes', $availableFixes);
    }
}
