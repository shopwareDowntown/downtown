<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586509275AlterVoucherUniqueConstraint extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585325412;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `sold_voucher`
            DROP INDEX `sold_voucher`;
        
        ALTER TABLE `sold_voucher`
            ADD UNIQUE KEY `uniq.order_line_item__code` (`order_line_item_id`,`code`);
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
