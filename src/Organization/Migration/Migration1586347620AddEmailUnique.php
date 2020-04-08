<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586347620AddEmailUnique extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586347620;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
ADD UNIQUE `email` (`email`);');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
