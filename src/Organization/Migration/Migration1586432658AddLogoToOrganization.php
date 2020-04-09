<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586432658AddLogoToOrganization extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586432658;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization`
            ADD `logo_id` binary(16) NULL AFTER `privacy`;'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
