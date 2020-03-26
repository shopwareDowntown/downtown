<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585230210MerchantShippingMethods extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585230210;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE `merchant_shipping_method` (
    `merchant_id` BINARY(16) NOT NULL,
    `shipping_method_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`merchant_id`,`shipping_method_id`),
    KEY `fk.merchant_shipping_method.merchant_id` (`merchant_id`),
    KEY `fk.merchant_shipping_method.shipping_method_id` (`shipping_method_id`),
    CONSTRAINT `fk.merchant_shipping_method.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.merchant_shipping_method.shipping_method_id` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_method` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
