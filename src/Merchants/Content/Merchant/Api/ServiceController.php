<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class ServiceController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $serviceRepository;

    public function __construct(EntityRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @OA\Get(
     *      path="/services",
     *      description="List all services",
     *      operationId="listServices",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/definitions/Service"),
     *          )
     *      )
     * )
     * @Route(name="merchant-api.account.list.services", path="/merchant-api/v{version}/services", methods={"GET"})
     */
    public function services(SalesChannelContext $context): JsonResponse
    {
        $criteria = new Criteria();
        return new JsonResponse($this->serviceRepository->search($criteria, $context->getContext())->getEntities());
    }
}
