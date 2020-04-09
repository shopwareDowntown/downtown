<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586435774ResetPasswordFixUpdatedAt extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586435774;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `organization_reset_password`
CHANGE `updated_at` `updated_at` datetime(3) NULL AFTER `created_at`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
