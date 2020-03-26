<?php
declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryPackage;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyEntity;

class DeliveryPackageEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $deliveryBoyId;

    /**
     * @var DeliveryBoyEntity|null
     */
    protected $deliveryBoy;

    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $status;

    public function getDeliveryBoyId(): ?string
    {
        return $this->deliveryBoyId;
    }

    public function setDeliveryBoyId(?string $deliveryBoyId): void
    {
        $this->deliveryBoyId = $deliveryBoyId;
    }

    public function getDeliveryBoy(): ?DeliveryBoyEntity
    {
        return $this->deliveryBoy;
    }

    public function setDeliveryBoy(?DeliveryBoyEntity $deliveryBoy): void
    {
        $this->deliveryBoy = $deliveryBoy;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
