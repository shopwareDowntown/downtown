<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\PlatformRequest;
use Shopware\Production\Organization\Exception\InvalidCredentialsException;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class LoginController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    public function __construct(EntityRepositoryInterface $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @OA\Post(
     *      path="/login",
     *      description="Login as organization",
     *      operationId="login",
     *      tags={"Organization"},
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
     * @Route(name="organization-api.login", path="/organization-api/v{version}/login", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function login(RequestDataBag $dataBag): JsonResponse
    {
        if (!$dataBag->has('email') || !$dataBag->has('password')) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $dataBag->get('email')));

        /** @var OrganizationEntity|null $organization */
        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$organization) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        if (!password_verify($dataBag->get('password'), $organization->getPassword())) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $token = Random::getAlphanumericString(32);

        $this->organizationRepository->update([
            [
                'id' => $organization->getId(),
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
