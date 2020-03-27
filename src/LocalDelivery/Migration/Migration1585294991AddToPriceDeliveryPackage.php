<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585294991AddToPriceDeliveryPackage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585294991;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
           ALTER TABLE `delivery_package` ADD COLUMN `price` FLOAT NOT NULL;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
