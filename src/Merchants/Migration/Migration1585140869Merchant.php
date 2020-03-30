<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585140869Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585140869;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('
            DROP TABLE merchant;
        ');

        $connection->executeQuery('
            CREATE TABLE `merchant` (
                `id` BINARY(16) NOT NULL,
                `public` TINYINT(1) NULL DEFAULT \'0\',
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `website` VARCHAR(255) NULL,
                `description` VARCHAR(255) NULL,
                `phone_number` VARCHAR(255) NULL,
                `customer_id` BINARY(16) NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                `category_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.merchant.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeQuery('
            CREATE TABLE `merchant_product` (
                `merchant_id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL,
                `product_version_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                PRIMARY KEY (`merchant_id`,`product_id`,`product_version_id`),
                KEY `fk.merchant_product.merchant_id` (`merchant_id`),
                KEY `fk.merchant_product.product_id` (`product_id`,`product_version_id`),
                CONSTRAINT `fk.merchant_product.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.merchant_product.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
