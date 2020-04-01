<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class InvalidCredentialsException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'MERCHANT_API_LOGIN_INVALID_CREDENTIALS';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
