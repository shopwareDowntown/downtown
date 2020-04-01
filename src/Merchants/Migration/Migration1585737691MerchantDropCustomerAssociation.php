<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585737691MerchantDropCustomerAssociation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585737691;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant`
DROP FOREIGN KEY `fk.merchant.customer_id`');
        $connection->executeQuery('DELETE FROM customer WHERE id IN (SELECT customer_id FROM merchant)');
        $connection->executeQuery('ALTER TABLE `merchant`
DROP `customer_id`;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
