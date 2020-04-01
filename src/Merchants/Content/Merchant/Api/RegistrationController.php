<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Services\RegistrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class RegistrationController
{
    /**
     * @var RegistrationService
     */
    private $registrationService;

    /**
     * @var SalesChannelContextFactory
     */
    private $salesChannelContextFactory;

    public function __construct(
        RegistrationService $registrationService,
        SalesChannelContextFactory $salesChannelContextFactory
    ) {
        $this->registrationService = $registrationService;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    /**
     * @Route(name="merchant-api.account.register.save", path="/merchant-api/v{version}/register", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function register(RequestDataBag $requestData): JsonResponse
    {
        $context = $this->salesChannelContextFactory->create(Random::getAlphanumericString(16), $requestData->get('salesChannelId'));

        $data = $requestData->only('publicCompanyName', 'email', 'password', 'salesChannelId');
        $this->registrationService->registerMerchant($data, $context);

        return new JsonResponse(['success' => true]);
    }
}
