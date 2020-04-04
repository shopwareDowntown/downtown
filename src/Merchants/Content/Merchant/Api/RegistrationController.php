<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Merchants\Content\Merchant\Services\RegistrationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelEntityRepository;

    public function __construct(
        RegistrationService $registrationService,
        SalesChannelContextFactory $salesChannelContextFactory,
        EntityRepositoryInterface $salesChannelEntityRepository,
        TranslatorInterface $translator
    ) {
        $this->registrationService = $registrationService;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->salesChannelEntityRepository = $salesChannelEntityRepository;
        $this->translator = $translator;
    }

    /**
     * @Route(name="merchant-api.account.register.save", path="/merchant-api/v{version}/register", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function register(RequestDataBag $requestData): JsonResponse
    {
        $context = $this->salesChannelContextFactory->create(Random::getAlphanumericString(16), $requestData->get('salesChannelId'));
        $salesChannel = $this->fetchSalesChannel($context->getSalesChannel()->getId());

        $this->translator->injectSettings(
            $context->getSalesChannel()->getId(),
            $salesChannel->getLanguageId(),
            $salesChannel->getLanguage()->getLocale()->getCode(),
            $context->getContext()
        );

        $data = $requestData->only('publicCompanyName', 'email', 'password', 'salesChannelId');
        $this->registrationService->registerMerchant($data, $context);

        return new JsonResponse(['success' => true]);
    }

    private function fetchSalesChannel(string $salesChannelId): SalesChannelEntity
    {
        $criteria = new Criteria([$salesChannelId]);
        $criteria->addAssociation('language.locale');

        return $this->salesChannelEntityRepository->search($criteria, \Shopware\Core\Framework\Context::createDefaultContext())->first();
    }
}
