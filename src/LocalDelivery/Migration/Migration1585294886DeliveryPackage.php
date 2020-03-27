<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585294886DeliveryPackage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585294886;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE IF NOT EXISTS `delivery_package` (
                `id` BINARY(16) NOT NULL,
                `content` LONGTEXT NOT NULL,
                `status` VARCHAR(255) NOT NULL,
                `comment` LONGTEXT NULL,
                `delivery_boy_id` BINARY(16) NULL,
                `shipping_method_id` BINARY(16) NULL,
                `merchant_id` BINARY(16) NULL,
                `recipient_title` VARCHAR(255) NULL,
                `recipient_first_name` VARCHAR(255) NOT NULL,
                `recipient_last_name` VARCHAR(255) NOT NULL,
                `recipient_zipcode` VARCHAR(255) NOT NULL,
                `recipient_city` VARCHAR(255) NOT NULL,
                `recipient_street` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.delivery_package.delivery_boy_id`
                    FOREIGN KEY (`delivery_boy_id`) REFERENCES `delivery_boy` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.delivery_package.shipping_method_id`
                    FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_method` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk.delivery_package.merchant_id`
                    FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
