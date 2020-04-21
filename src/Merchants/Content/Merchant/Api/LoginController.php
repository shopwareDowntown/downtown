<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
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
     * @OA\Post(
     *      path="/login",
     *      description="Login as merchant",
     *      operationId="merchantLogin",
     *      tags={"Merchant"},
     *      @OA\Parameter(
     *        name="email",
     *        in="body",
     *        description="Email",
     *        @OA\Schema(type="string"),
     *      ),
     *      @OA\Parameter(
     *        name="password",
     *        in="body",
     *        description="Password",
     *        @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Token",
     *          @OA\JsonContent(ref="#/definitions/LoginResponse")
     *     )
     * )
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
        $criteria->addFilter(new EqualsFilter('activationCode', null));

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
