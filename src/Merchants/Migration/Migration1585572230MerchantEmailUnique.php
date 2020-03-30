<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585572230MerchantEmailUnique extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585572230;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('ALTER TABLE `merchant`
ADD UNIQUE `email` (`email`);');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
