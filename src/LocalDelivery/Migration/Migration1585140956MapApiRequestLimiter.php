<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1585140956MapApiRequestLimiter extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585140956;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `map_api_request_limiter` (
                `id` BINARY(16) NOT NULL,
                `endpoint_name` VARCHAR(255) NOT NULL,
                `request_count` INTEGER DEFAULT 0,
                `request_limit` INTEGER NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->insert('map_api_request_limiter', [
            'id' => Uuid::randomBytes(),
            'endpoint_name' => 'search-temporary-geocoding-api',
            'request_count' => 0,
            'request_limit' => 100000,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('map_api_request_limiter', [
            'id' => Uuid::randomBytes(),
            'endpoint_name' => 'navigation-optimization-api',
            'request_count' => 0,
            'request_limit' => 100000,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
