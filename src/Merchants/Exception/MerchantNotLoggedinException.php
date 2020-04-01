<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class MerchantNotLoggedinException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'MERCHANT_API_MERCHANT_NOT_LOGGED_IN';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
