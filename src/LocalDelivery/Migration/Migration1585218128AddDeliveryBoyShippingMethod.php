<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class Migration1585218128AddDeliveryBoyShippingMethod extends MigrationStep
{
    private $languageEnId;
    private $languageDeId;
    private $shippingMethodId;
    private $ruleId;
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
        $this->ruleId = Uuid::randomBytes();
        $this->deliveryTimeId = Uuid::randomBytes();

        $this->createRules($connection);
        $this->createDeliveryTimes($connection);
        $this->createShippingMethod($connection);
        $this->createShippingMethodPrice($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function createShippingMethod(Connection $connection): void
    {
        $connection->insert('shipping_method', [
            'id' => $this->shippingMethodId,
            'active' => 1,
            'availability_rule_id' => $this->ruleId,
            'delivery_time_id' => $this->deliveryTimeId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('shipping_method_translation', [
            'shipping_method_id' => $this->shippingMethodId,
            'language_id' => $this->languageEnId,
            'name' => 'Delivery Boy',
            'description' => 'This shipping method provides the direct delivery via delivery boy right out of your neighbourhood.',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('shipping_method_translation', [
            'shipping_method_id' => $this->shippingMethodId,
            'language_id' => $this->languageDeId,
            'name' => 'Lieferjunge',
            'description' => 'Diese Versandart bietet direkte Lieferung mittels Lieferjungen direkt aus der Nachbarschaft.',
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

    private function createRules(Connection $connection): void
    {
        $connection->insert('rule', [
            'id' => $this->ruleId,
            'name' => 'Cart >= 0',
            'priority' => 100,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $connection->insert('rule_condition', [
            'id' => Uuid::randomBytes(),
            'rule_id' => $this->ruleId,
            'type' => 'cartCartAmount',
            'value' => json_encode(['operator' => '>=', 'amount' => 0]),
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