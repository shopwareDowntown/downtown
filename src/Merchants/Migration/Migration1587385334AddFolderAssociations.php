<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1587385334AddFolderAssociations extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1587385334;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        UPDATE `media_default_folder`
        SET `association_fields` = '["merchantMedia"]'
        WHERE entity = 'merchants';

        UPDATE `media_default_folder`
        SET `association_fields` = '["organizationMedia"]'
        WHERE entity = 'organization';
SQL;

        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
