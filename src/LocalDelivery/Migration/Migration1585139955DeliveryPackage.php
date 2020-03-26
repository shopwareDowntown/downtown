<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585139955DeliveryPackage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585139955;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE `delivery_package` (
                `id` BINARY(16) NOT NULL,
                `recipient` LONGTEXT NOT NULL,
                `content` LONGTEXT NOT NULL,
                `status` VARCHAR(255) NOT NULL,
                `comment` LONGTEXT NULL,
                `delivery_boy_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.swag_planet_express_delivery_package.delivery_boy_id` (`delivery_boy_id`),
                CONSTRAINT `fk.swag_planet_express_delivery_package.delivery_boy_id` FOREIGN KEY (`delivery_boy_id`) REFERENCES `delivery_boy` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
