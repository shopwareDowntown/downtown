<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Checkout\SoldVoucher;

use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PriceDefinitionField;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

class SoldVoucherEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $orderLineItemId;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var PriceDefinitionInterface
     */
    protected $value;

    /**
     * @var \DateTimeInterface|null
     */
    protected $redeemedAt;

    /**
     * @var OrderLineItemEntity
     */
    protected $orderLineItem;

    /**
     * @var MerchantEntity
     */
    protected $merchant;

    /**
     * @return \DateTimeInterface|null
     */
    public function getRedeemedAt(): ?\DateTimeInterface
    {
        return $this->redeemedAt;
    }

    /**
     * @param  \DateTimeInterface|null  $redeemedAt
     */
    public function setRedeemedAt(?\DateTimeInterface $redeemedAt): void
    {
        $this->redeemedAt = $redeemedAt;
    }

    /**
     * @return PriceDefinitionInterface
     */
    public function getValue(): PriceDefinitionInterface
    {
        return $this->value;
    }

    /**
     * @param  PriceDefinitionInterface  $value
     */
    public function setValue(PriceDefinitionInterface $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOrderLineItemId(): string
    {
        return $this->orderLineItemId;
    }

    /**
     * @param  string  $orderLineItemId
     */
    public function setOrderLineItemId(string $orderLineItemId): void
    {
        $this->orderLineItemId = $orderLineItemId;
    }

    /**
     * @return OrderLineItemEntity
     */
    public function getOrderLineItem(): OrderLineItemEntity
    {
        return $this->orderLineItem;
    }

    /**
     * @param  OrderLineItemEntity  $orderLineItem
     */
    public function setOrderLineItem(OrderLineItemEntity $orderLineItem): void
    {
        $this->orderLineItem = $orderLineItem;
    }

    /**
     * @return MerchantEntity
     */
    public function getMerchant() : MerchantEntity
    {
        return $this->merchant;
    }

    /**
     * @param  MerchantEntity  $merchant
     */
    public function setMerchant(MerchantEntity $merchant) : void
    {
        $this->merchant = $merchant;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param  string  $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param  string  $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
