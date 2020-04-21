<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Events;

use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Contracts\EventDispatcher\Event;

class BlockPaymentMethodsEvent extends Event
{

    /**
     * @var PaymentMethodCollection
     */
    private $paymentMethods;

    /**
     * @var MerchantEntity
     */
    private $merchantEntity;

    /**
     * @var SalesChannelContext
     */
    private $context;


    public function __construct(PaymentMethodCollection $paymentMethods, MerchantEntity $merchantEntity, SalesChannelContext $context)
    {
        $this->paymentMethods = $paymentMethods;
        $this->merchantEntity = $merchantEntity;
        $this->context = $context;
    }

    public function getPaymentMethodCollection(): PaymentMethodCollection
    {
        return $this->paymentMethods;
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
