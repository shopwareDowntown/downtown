<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586354228ProfileFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586354228;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
ADD `first_name` varchar(255) NOT NULL AFTER `sales_channel_id`,
ADD `last_name` varchar(255) NOT NULL AFTER `first_name`,
ADD `phone` varchar(255) NULL AFTER `last_name`,
ADD `post_code` varchar(255) NULL AFTER `phone`,
ADD `city` varchar(255) NULL AFTER `post_code`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
