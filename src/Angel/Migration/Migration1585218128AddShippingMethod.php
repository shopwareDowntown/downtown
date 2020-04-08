<?php declare(strict_types=1);

namespace Shopware\Production\Angel\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class Migration1585218128AddShippingMethod extends MigrationStep
{
    private $languageEnId;
    private $languageDeId;
    private $shippingMethodId;
    private $deliveryTimeId;

    public function getCreationTimestamp(): int
    {
        return 1585218128;
    }

    public function update(Connection $connection): void
    {
        $this->languageEnId = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $this->languageDeId = $this->getLanguageIdDe($connection);
        $this->shippingMethodId = Uuid::randomBytes();
        $this->deliveryTimeId = Uuid::randomBytes();

        $this->createDeliveryTimes($connection);
        $this->createShippingMethod($connection);
        $this->createShippingMethodPrice($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function createShippingMethod(Connection $connection): void
    {
        $rule = (string) $connection->fetchColumn('SELECT id FROM rule WHERE `name` = "Always valid (Default)"');

        $connection->insert('shipping_method', [
            'id' => $this->shippingMethodId,
            'availability_rule_id' => $rule,
            'active' => 1,
            'delivery_time_id' => $this->deliveryTimeId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('shipping_method_translation', [
            'shipping_method_id' => $this->shippingMethodId,
            'language_id' => $this->languageEnId,
            'name' => 'Angel',
            'description' => '',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('shipping_method_translation', [
            'shipping_method_id' => $this->shippingMethodId,
            'language_id' => $this->languageDeId,
            'name' => 'Angel',
            'description' => '',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    private function createDeliveryTimes(Connection $connection): void
    {
        $connection->insert('delivery_time', [
            'id' => $this->deliveryTimeId,
            'min' => 0,
            'max' => 3,
            'unit' => DeliveryTimeEntity::DELIVERY_TIME_DAY,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('delivery_time_translation', [
            'delivery_time_id' => $this->deliveryTimeId,
            'language_id' => $this->languageEnId,
            'name' => 'As soon as possible',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('delivery_time_translation', [
            'delivery_time_id' => $this->deliveryTimeId,
            'language_id' => $this->languageDeId,
            'name' => 'So bald wie möglich',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    private function createShippingMethodPrice(Connection $connection): void
    {
        $connection->insert('shipping_method_price', [
            'id' => Uuid::randomBytes(),
            'shipping_method_id' => $this->shippingMethodId,
            'calculation' => 1,
            'currency_id' => Uuid::fromHexToBytes(Defaults::CURRENCY),
            'price' => 0,
            'quantity_start' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    private function getLanguageIdDe(Connection $connection): string
    {
        return (string) $connection->fetchColumn(
            'SELECT id FROM language WHERE id != :default',
            ['default' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)]
        );
    }
}
