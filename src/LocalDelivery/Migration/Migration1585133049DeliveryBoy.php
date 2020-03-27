<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585133049DeliveryBoy extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585133049;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `delivery_boy` (
                `id` BINARY(16) NOT NULL,
                `title` VARCHAR(255) NULL,
                `first_name` VARCHAR(255) NOT NULL,
                `last_name` VARCHAR(255) NOT NULL,
                `password` VARCHAR(1024) NULL,
                `email` VARCHAR(255) NOT NULL,
                `active` TINYINT(1) NULL DEFAULT "0",
                `zipcode` VARCHAR(255) NOT NULL,
                `city` VARCHAR(255) NOT NULL,
                `street` VARCHAR(255) NOT NULL,
                `phone_number` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
