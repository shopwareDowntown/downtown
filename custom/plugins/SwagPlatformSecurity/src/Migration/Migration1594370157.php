<?php declare(strict_types=1);

namespace Swag\Security\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1594370157 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1594370157;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('
CREATE TABLE `swag_security_config` (
  `ticket` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
