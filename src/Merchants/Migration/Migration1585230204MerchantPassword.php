<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585230204MerchantPassword extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585230204;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `merchant`
            ADD `password` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ;
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
