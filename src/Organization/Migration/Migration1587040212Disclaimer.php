<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1587040212Disclaimer extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587040212;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE `organization_disclaimer` (
    `id` BINARY(16) NOT NULL,
    `active` TINYINT(1) NULL DEFAULT \'0\',
    `text` VARCHAR(255) NULL,
    `image_id` BINARY(16) NULL,
    `organization_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
