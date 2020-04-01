<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585746565MerchantMakeUpdatedNullable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585746565;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant_access_token`
CHANGE `updated_at` `updated_at` datetime(3) NULL AFTER `created_at`;');

        $connection->executeQuery('ALTER TABLE `merchant_reset_password_token`
CHANGE `updated_at` `updated_at` datetime(3) NULL AFTER `created_at`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
