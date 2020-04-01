<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\PlatformRequest;
use Shopware\Production\Merchants\Content\Merchant\Exception\InvalidCredentialsException;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class LoginController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(EntityRepositoryInterface $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Route(name="merchant-api.merchant.login", path="/merchant-api/v{version}/login", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function login(RequestDataBag $dataBag): JsonResponse
    {
        if (!$dataBag->has('email') || !$dataBag->has('password')) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $dataBag->get('email')));
        $criteria->addFilter(new EqualsFilter('active', 1));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$merchant) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        if (!password_verify($dataBag->get('password'), $merchant->getPassword())) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $token = Random::getAlphanumericString(32);

        $this->merchantRepository->update([
            [
                'id' => $merchant->getId(),
                'accessTokens' => [
                    [
                        'token' => $token
                    ]
                ],
            ]
        ], Context::createDefaultContext());

        return new JsonResponse([
            PlatformRequest::HEADER_CONTEXT_TOKEN => $token
        ]);
    }
}
