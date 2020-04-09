<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586422570ResetPassword extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586422570;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE `organization_reset_password` (
    `id` BINARY(16) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `organization_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk.organization_reset_password.organization_id` (`organization_id`),
    CONSTRAINT `fk.organization_reset_password.organization_id` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
