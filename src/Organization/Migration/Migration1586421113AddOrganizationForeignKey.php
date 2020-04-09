<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586421113AddOrganizationForeignKey extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586421113;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
            ADD FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
