<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586425200ImprintPrivacyTos extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586425200;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
ADD `imprint` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `city`,
ADD `tos` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `imprint`,
ADD `privacy` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `tos`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
