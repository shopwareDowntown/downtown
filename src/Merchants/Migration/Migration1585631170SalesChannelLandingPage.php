<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585631170SalesChannelLandingPage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585631170;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
CREATE TABLE IF NOT EXISTS `sales_channel_landing_page` (
    `id`               BINARY(16)  NOT NULL,
    `sales_channel_id` BINARY(16)  NOT NULL,
    `cms_page_id`      BINARY(16)  NOT NULL,
    `created_at`       DATETIME(3) NOT NULL,
    `updated_at`       DATETIME(3) NULL,
    PRIMARY KEY (`id`,`sales_channel_id`, `cms_page_id`),
    KEY `fk.sales_channel_landing_page.sales_channel_id` (`sales_channel_id`),
    KEY `fk.sales_channel_landing_page.cms_page_id` (`cms_page_id`),
    CONSTRAINT `fk.sales_channel_landing_page.sales_channel_id` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.sales_channel_landing_page.cms_page_id` FOREIGN KEY (`cms_page_id`) REFERENCES `cms_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeQuery($query);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
