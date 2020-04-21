<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1586750381RevertVoucherUniqueConstraint extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585325412;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `sold_voucher`
            DROP INDEX `uniq.order_line_item__code`;

        ALTER TABLE `sold_voucher`
            ADD UNIQUE KEY `uniq.merchant_id__code` (`merchant_id`,`code`);
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
