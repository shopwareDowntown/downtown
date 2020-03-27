<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585302577MerchantMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585302577;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('
        CREATE TABLE IF NOT EXISTS `merchant_media` (
            `merchant_id` BINARY(16) NOT NULL,
            `media_id` BINARY(16) NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            PRIMARY KEY (`merchant_id`,`media_id`),
            KEY `fk.merchant_media.merchant_id` (`merchant_id`),
            KEY `fk.merchant_media.media_id` (`media_id`),
            CONSTRAINT `fk.merchant_media.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.merchant_media.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
