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
    ) {
        $this->registrationService = $registrationService;
    }

    /**
     * @Route(name="merchant-api.account.register.save", path="/merchant-api/v{version}/register", methods={"POST"}, defaults={"csrf_protected"=false})
     */
    public function register(RequestDataBag $requestData, SalesChannelContext $context): JsonResponse
    {
        $data = $requestData->only('name', 'email', 'password', 'salesChannelId');
        $this->registrationService->registerMerchant($data, $context);

        return new JsonResponse(['success' => true]);
    }
}
