<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586438427MerchantAddImprintPrivacyTosRevocation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586438427;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant`
            ADD `imprint` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `city`,
            ADD `tos` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `imprint`,
            ADD `privacy` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `tos`,
            ADD `revocation` longtext COLLATE \'utf8mb4_unicode_ci\' NULL AFTER `privacy`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
