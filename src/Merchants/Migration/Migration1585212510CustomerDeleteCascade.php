<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585212510CustomerDeleteCascade extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585212510;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE merchant
            ADD CONSTRAINT `fk.merchant.customer_id` FOREIGN KEY (`customer_id`)
            REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

        ALTER TABLE customer
            ADD CONSTRAINT `fk.customer.merchant.customer_id` FOREIGN KEY (`id`)
            REFERENCES `merchant` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
