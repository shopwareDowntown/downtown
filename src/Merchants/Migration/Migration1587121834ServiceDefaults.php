<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1587121834ServiceDefaults extends MigrationStep
{
    private const DEFAULT_SERVICES = [
        [
            'de' => 'Lieferung nach Hause',
            'en' => 'Delivery to Home'
        ],
        [
            'de' => 'Abholung',
            'en' => 'Pick up'
        ],
        [
            'de' => 'Versand',
            'en' => 'Shipping'
        ],
        [
            'de' => 'Remote-Beratung',
            'en' => 'Remote consulting'
        ],
    ];

    public function getCreationTimestamp(): int
    {
        return 1587121834;
    }

    public function update(Connection $connection): void
    {
        $languageEnId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageDeId = $this->getLanguageIdDe($connection);

        foreach (self::DEFAULT_SERVICES as $service) {
            $serviceId = Uuid::randomBytes();

            $connection->insert('service', [
                'id' => $serviceId,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);

            $connection->insert('service_translation', [
                'name' => $service['en'],
                'language_id' => $languageEnId,
                'service_id' => $serviceId,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);

            $connection->insert('service_translation', [
                'name' => $service['de'],
                'language_id' => $languageDeId,
                'service_id' => $serviceId,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function getLanguageIdDe(Connection $connection): string
    {
        return (string)$connection->fetchColumn(
            'SELECT id FROM language WHERE id != :default',
            ['default' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)]
        );
    }
}
