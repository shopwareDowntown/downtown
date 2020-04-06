<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1586000282MerchantAddSeoTemplate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1586000282;
    }

    public function update(Connection $connection): void
    {
        $connection->insert(
            'seo_url_template',
            [
                'id' => Uuid::randomBytes(),
                'route_name' => 'storefront.merchant.detail',
                'entity_name' => 'merchant',
                'template' => '{{ merchant.publicCompanyName }}',
                'is_valid' => 1,
                'created_at' => date(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
