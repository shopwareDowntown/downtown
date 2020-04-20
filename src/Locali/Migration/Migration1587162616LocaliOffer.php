<?php declare(strict_types=1);

namespace Shopware\Production\Locali\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * Class Migration1587162616LocaliOffer
 * @package Shopware\Production\Locali\Migration
 */
class Migration1587162616LocaliOffer extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587162616;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        ALTER TABLE `merchant`
        ADD COLUMN `locali_offer_document_id` VARCHAR(255)
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
