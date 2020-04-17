<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1587121064Service extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587121064;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('CREATE TABLE `service` (
    `id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_translation` (
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `service_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`service_id`,`language_id`),
    KEY `fk.service_translation.service_id` (`service_id`),
    KEY `fk.service_translation.language_id` (`language_id`),
    CONSTRAINT `fk.service_translation.service_id` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.service_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

        $connection->executeQuery('CREATE TABLE `merchant_service` (
    `merchant_id` BINARY(16) NOT NULL,
    `service_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`merchant_id`,`service_id`),
    KEY `fk.merchant_service.merchant_id` (`merchant_id`),
    KEY `fk.merchant_service.service_id` (`service_id`),
    CONSTRAINT `fk.merchant_service.merchant_id` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.merchant_service.service_id` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
