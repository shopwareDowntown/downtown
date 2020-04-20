<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class LogoutController
{
    private $merchantAccessTokenRepository;

    public function __construct(EntityRepositoryInterface $merchantAccessTokenRepository)
    {
        $this->merchantAccessTokenRepository = $merchantAccessTokenRepository;
    }

    /**
     * @OA\Post(
     *      path="/logout",
     *      description="Logout",
     *      operationId="Logout",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          description="Logout",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="merchant-api.merchant.logout", path="/merchant-api/v{version}/logout", methods={"POST"})
     */
    public function logout(Request $request): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('token', $request->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN)));

        $ids = $this->merchantAccessTokenRepository->searchIds($criteria, Context::createDefaultContext());

        if ($ids->getTotal() === 0) {
            return new JsonResponse(['success' => true]);
        }

        $this->merchantAccessTokenRepository->delete(array_map(function (string $id) {
            return ['id' => $id];
        }, $ids->getIds()), Context::createDefaultContext());

        return new JsonResponse(['success' => true]);
    }
}
