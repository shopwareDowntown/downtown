<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585140956MapApiRequestLimiter extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585140956;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE `map_api_request_limiter` (
                `id` BINARY(16) NOT NULL,
                `endpoint_name` VARCHAR(255) NOT NULL,
                `count` INTEGER DEFAULT 0,
                `limit` INTEGER NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
