<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Services\RegistrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class RegistrationController
{
    /**
     * @var RegistrationService
     */
    private $registrationService;

    public function __construct(
        RegistrationService $registrationService
    )
    {
        $this->registrationService = $registrationService;
    }

    /**
     * @Route(name="merchant-api.account.register.save", path="/merchant-api/register")
     */
    public function register(RequestDataBag $requestData, SalesChannelContext $context): JsonResponse
    {
        $merchantId = $this->registrationService->registerMerchant($requestData,  $context);

        return new JsonResponse(['data' => $merchantId]);
    }
}
