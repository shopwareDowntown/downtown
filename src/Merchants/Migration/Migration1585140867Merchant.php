<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585140867Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585140867;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE `merchant` (
              `id` BINARY(16) NOT NULL,
              `customer_id` BINARY(16) NOT NULL,
              `website` VARCHAR(255) NULL,
              `phone_number` VARCHAR(255) NULL,
              `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              PRIMARY KEY (`id`),
              CONSTRAINT `fk.merchant.customer_id` FOREIGN KEY (`customer_id`)
                REFERENCES `customer` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
