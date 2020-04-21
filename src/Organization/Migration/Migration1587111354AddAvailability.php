<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1587111354AddAvailability extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587111354;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant`
ADD `availability` tinyint(1) DEFAULT \'0\';');
        $connection->executeQuery('ALTER TABLE `merchant`
ADD `availability_text` varchar(1024) NULL;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
