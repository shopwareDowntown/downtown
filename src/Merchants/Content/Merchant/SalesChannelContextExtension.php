<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class SalesChannelContextExtension extends Struct
{
    private const NAME = 'merchant';

    /**
     * @var MerchantEntity
     */
    private $merchant;

    public static function add(SalesChannelContext $salesChannelContext, MerchantEntity $merchant): void
    {
        $salesChannelContext->addExtension(self::NAME, new self($merchant));
    }

    public static function extract(SalesChannelContext $salesChannelContext): MerchantEntity
    {
        $extension = $salesChannelContext->getExtension(self::NAME);

        if (!$extension || !$extension instanceof self) {
            throw new CustomerNotLoggedInException();
        }

        return $extension->merchant;
    }

    public function __construct(MerchantEntity $merchant)
    {
        $this->merchant = $merchant;
    }
}
