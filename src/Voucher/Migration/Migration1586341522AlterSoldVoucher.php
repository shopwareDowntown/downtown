<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586341522AlterSoldVoucher extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586341522;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('
            DROP TABLE IF EXISTS `sold_voucher`;
        ');

        $connection->executeQuery('CREATE TABLE `sold_voucher` (
    `id` BINARY(16) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `value` JSON NULL,
    `merchant_id` BINARY(16) NULL,
    `order_line_item_id` BINARY(16) NULL,
    `redeemed_at` DATETIME(3) NULL,
    `order_line_item_version_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq.merchant_id__code` (`merchant_id`,`code`),
    CONSTRAINT `json.sold_voucher.value` CHECK (JSON_VALID(`value`)),
    KEY `fk.sold_voucher.order_line_item_id` (`order_line_item_id`,`order_line_item_version_id`),
    KEY `fk.sold_voucher.merchant_id` (`merchant_id`),
    CONSTRAINT `fk.sold_voucher.order_line_item_id` FOREIGN KEY (`order_line_item_id`,`order_line_item_version_id`) REFERENCES `order_line_item` (`id`,`version_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk.sold_voucher.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        //
    }
}
