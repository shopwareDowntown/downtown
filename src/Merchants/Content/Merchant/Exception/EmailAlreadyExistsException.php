<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class EmailAlreadyExistsException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'MERCHANT_EMAIL_ALREADY_EXISTS';
    }
}
