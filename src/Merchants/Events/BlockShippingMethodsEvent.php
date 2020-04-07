<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Events;

use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Contracts\EventDispatcher\Event;

class BlockShippingMethodsEvent extends Event
{
    /**
     * @var ShippingMethodCollection
     */
    private $shippingMethodCollection;

    /**
     * @var MerchantEntity
     */
    private $merchantEntity;

    /**
     * @var SalesChannelContext
     */
    private $context;

    public function __construct(ShippingMethodCollection $shippingMethodCollection, MerchantEntity $merchantEntity, SalesChannelContext $context)
    {
        $this->shippingMethodCollection = $shippingMethodCollection;
        $this->merchantEntity = $merchantEntity;
        $this->context = $context;
    }

    public function getShippingMethodCollection(): ShippingMethodCollection
    {
        return $this->shippingMethodCollection;
    }

    public function getMerchantEntity(): MerchantEntity
    {
        return $this->merchantEntity;
    }

    public function getContext(): SalesChannelContext
    {
        return $this->context;
    }
}
