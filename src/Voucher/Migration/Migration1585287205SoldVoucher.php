<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585287205SoldVoucher extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585287205;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE IF NOT EXISTS `sold_voucher` (
    `id` BINARY(16) NOT NULL,
    `merchant_id` BINARY(16) NOT NULL,
    `order_line_item_id` BINARY(16) NOT NULL,
    `code` CHAR(10) NOT NULL,
    `name` VARCHAR (255) NOT NULL,
    `value` JSON NOT NULL,
    `redeemed_at` DATETIME(3) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `sold_voucher` (`merchant_id`,`code`),
    KEY `fk.sold_voucher.order_line_item_id` (`order_line_item_id`),
    KEY `fk.sold_voucher.merchant_id` (`merchant_id`),
    CONSTRAINT `fk.sold_voucher.order_line_item_id` FOREIGN KEY (`order_line_item_id`) REFERENCES `order_line_item` (`id`),
    CONSTRAINT `fk.sold_voucher.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        //
    }
}
