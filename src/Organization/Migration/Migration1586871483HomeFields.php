<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586871483HomeFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586871483;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
ADD `home_hero_image_id` binary(16) NULL AFTER `logo_id`,
ADD `home_text` longtext NULL AFTER `home_hero_image_id`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
