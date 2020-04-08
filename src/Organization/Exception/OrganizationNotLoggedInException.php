<?php declare(strict_types=1);


namespace Shopware\Production\Organization\Exception;


use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class OrganizationNotLoggedInException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'ORGANIZATION_NOT_LOGGED_IN';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
