<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585658996MerchantRenameCountryId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585658996;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('UPDATE merchant SET country = NULL');
        $connection->executeQuery('ALTER TABLE `merchant`
CHANGE `country` `country_id` binary(16) NULL AFTER `city`;');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
