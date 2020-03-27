<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585134142DeliveryBoySessionId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585133049;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
             ALTER TABLE `delivery_boy` ADD COLUMN `session_id` VARCHAR(255) NULL;'
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
