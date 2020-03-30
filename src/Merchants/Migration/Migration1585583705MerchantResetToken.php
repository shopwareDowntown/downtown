<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585583705MerchantResetToken extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585583705;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE `merchant_reset_password_token` (
    `id` BINARY(16) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `merchant_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk.merchant_reset_password_token.merchant_id` (`merchant_id`),
    CONSTRAINT `fk.merchant_reset_password_token.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
