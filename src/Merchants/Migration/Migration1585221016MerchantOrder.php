<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585221016MerchantOrder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585221016;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE IF NOT EXISTS `merchant_order` (
    `merchant_id` BINARY(16) NOT NULL,
    `order_id` BINARY(16) NOT NULL,
    `order_version_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`merchant_id`,`order_id`,`order_version_id`),
    KEY `fk.merchant_order.merchant_id` (`merchant_id`),
    KEY `fk.merchant_order.order_id` (`order_id`,`order_version_id`),
    CONSTRAINT `fk.merchant_order.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.merchant_order.order_id` FOREIGN KEY (`order_id`,`order_version_id`) REFERENCES `order` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
