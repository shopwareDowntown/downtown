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

    public function getRedeemedAt(): ?\DateTimeInterface
    {
        return $this->redeemedAt;
    }

    public function setRedeemedAt(?\DateTimeInterface $redeemedAt): void
    {
        $this->redeemedAt = $redeemedAt;
    }

    public function getValue(): PriceDefinitionInterface
    {
        return $this->value;
    }

    public function setValue(PriceDefinitionInterface $value): void
    {
        $this->value = $value;
    }

    public function getOrderLineItemId(): string
    {
        return $this->orderLineItemId;
    }

    public function setOrderLineItemId(string $orderLineItemId): void
    {
        $this->orderLineItemId = $orderLineItemId;
    }

    public function getOrderLineItem(): OrderLineItemEntity
    {
        return $this->orderLineItem;
    }

    public function setOrderLineItem(OrderLineItemEntity $orderLineItem): void
    {
        $this->orderLineItem = $orderLineItem;
    }

    public function getMerchant(): MerchantEntity
    {
        return $this->merchant;
    }

    public function setMerchant(MerchantEntity $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
