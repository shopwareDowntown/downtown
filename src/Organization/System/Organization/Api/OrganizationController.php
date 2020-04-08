<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class OrganizationController
{
    /**
     * @OA\Get(
     *      path="/organization",
     *      description="Get loggedin organization",
     *      operationId="loadOrganization",
     *      tags={"Account"},
     *      @OA\Response(
     *          response="200",
     *          description="Token",
     *          @OA\JsonContent(ref="#/components/schemas/OrganizationEntity")
     *     )
     * )
     * @Route(name="organization-api.organization", path="/organization-api/v{version}/organization", methods={"GET"})
     */
    public function load(OrganizationEntity $organizationEntity): JsonResponse
    {
        return new JsonResponse($organizationEntity);
    }
}
