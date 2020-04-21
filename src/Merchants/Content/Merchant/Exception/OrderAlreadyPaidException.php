<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class OrderAlreadyPaidException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'MERCHANT_API_ORDER_ALREADY_PAID';
    }
}
