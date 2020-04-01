<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585738104MerchantActiveField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585738104;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant`
ADD `active` tinyint(1) NOT NULL DEFAULT "0" AFTER `id`,
ADD `activation_code` varchar(1024) COLLATE \'utf8mb4_unicode_ci\' NULL;');
        $connection->executeQuery('UPDATE merchant SET active = 1');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
