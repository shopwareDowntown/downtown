<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryPackage;

use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyEntity;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

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
    protected $content;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $recipientTitle;

    /**
     * @var string
     */
    protected $recipientFirstName;

    /**
     * @var string
     */
    protected $recipientLastName;

    /**
     * @var string
     */
    protected $recipientStreet;

    /**
     * @var string
     */
    protected $recipientZipcode;

    /**
     * @var string
     */
    protected $recipientCity;

    /**
     * @var string|null
     */
    protected $shippingMethodId;

    /**
     * @var ShippingMethodEntity|null
     */
    protected $shippingMethod;

    /**
     * @var string|null
     */
    protected $merchantId;

    /**
     * @var MerchantEntity|null
     */
    protected $merchant;

    /**
     * @var float
     */
    protected $price;

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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRecipientTitle(): string
    {
        return $this->recipientTitle;
    }

    public function setRecipientTitle(string $recipientTitle): void
    {
        $this->recipientTitle = $recipientTitle;
    }

    public function getRecipientFirstName(): string
    {
        return $this->recipientFirstName;
    }

    public function setRecipientFirstName(string $recipientFirstName): void
    {
        $this->recipientFirstName = $recipientFirstName;
    }

    public function getRecipientLastName(): string
    {
        return $this->recipientLastName;
    }

    public function setRecipientLastName(string $recipientLastName): void
    {
        $this->recipientLastName = $recipientLastName;
    }

    public function getRecipientStreet(): string
    {
        return $this->recipientStreet;
    }

    public function setRecipientStreet(string $recipientStreet): void
    {
        $this->recipientStreet = $recipientStreet;
    }

    public function getRecipientZipcode(): string
    {
        return $this->recipientZipcode;
    }

    public function setRecipientZipcode(string $recipientZipcode): void
    {
        $this->recipientZipcode = $recipientZipcode;
    }

    public function getRecipientCity(): string
    {
        return $this->recipientCity;
    }

    public function setRecipientCity(string $recipientCity): void
    {
        $this->recipientCity = $recipientCity;
    }

    public function getShippingMethodId(): ?string
    {
        return $this->shippingMethodId;
    }

    public function setShippingMethodId(?string $shippingMethodId): void
    {
        $this->shippingMethodId = $shippingMethodId;
    }

    public function getShippingMethod(): ?ShippingMethodEntity
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(?ShippingMethodEntity $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    public function getMerchant(): ?MerchantEntity
    {
        return $this->merchant;
    }

    public function setMerchant(?MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
