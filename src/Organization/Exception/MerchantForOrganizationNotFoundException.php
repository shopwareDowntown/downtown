<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class MerchantForOrganizationNotFoundException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'ORGANIZATION_MERCHANT_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
