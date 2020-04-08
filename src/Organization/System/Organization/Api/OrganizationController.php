<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Shopware\Production\Organization\Exception\MerchantForOrganizationNotFoundException;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class OrganizationController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        EntityRepositoryInterface $organizationRepository,
        EntityRepositoryInterface $merchantRepository
    ) {
        $this->salesChannelRepository = $salesChannelRepository;
        $this->organizationRepository = $organizationRepository;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @OA\Get(
     *      path="/organizations",
     *      description="Get all organizations",
     *      operationId="loadAllOrganization",
     *      tags={"List"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/OrganizationEntity")
     *          )
     *     )
     * )
     * @Route(name="organization-api.organizations", path="/organization-api/v{version}/organizations", methods={"GET"}, defaults={"auth_required"=false})
     */
    public function load(): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        /** @var SalesChannelCollection $salesChannelCollection */
        $salesChannelCollection = $this->salesChannelRepository->search($criteria, Context::createDefaultContext());

        $result = [];
        foreach ($salesChannelCollection as $salesChannel) {
            $domainCollection = $salesChannel->getDomains();
            if ($domainCollection === null) {
                continue;
            }

            $domainEntity = $domainCollection->first();
            if ($domainEntity === null) {
                continue;
            }

            $result[] = [
                'id' => $salesChannel->getId(),
                'name' => $salesChannel->getName(),
                'domain' => $domainEntity->getUrl(),
                'accessKey' => $salesChannel->getAccessKey(),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @OA\Get(
     *      path="/organization",
     *      description="Get logged in organization",
     *      operationId="loadOrganization",
     *      tags={"List"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/components/schemas/OrganizationEntity")
     *     )
     * )
     * @Route(name="organization-api.organization", path="/organization-api/v{version}/organization", methods={"GET"})
     */
    public function loadOne(OrganizationEntity $organizationEntity): JsonResponse
    {
        return new JsonResponse($organizationEntity);
    }

    /**
     * @OA\Get(
     *      path="/organizations/merchants",
     *      description="Get all merchants for organization",
     *      operationId="loadAllOrganizationMerchants",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MerchantEntity")
     *          )
     *     )
     * )
     * @Route(name="organization-api.organization.merchants", path="/organization-api/v{version}/organization/merchants", methods={"GET"})
     */
    public function getMerchants(OrganizationEntity $organizationEntity): JsonResponse
    {
        $criteria = new Criteria([$organizationEntity->getId()]);
        $criteria->addAssociation('salesChannel.merchants');

        /** @var OrganizationEntity $organization */
        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        return new JsonResponse(array_values($organization->getSalesChannel()->get('merchants')->getElements()));
    }

    /**
     * @OA\Post(
     *      path="/organization/merchant/{merchantId}/set-active",
     *      description="Set merchant active state",
     *      operationId="merchantActive",
     *      tags={"Merchant"},
     *      @OA\Parameter(
     *        name="active",
     *        in="body",
     *        description="Active",
     *        @OA\Schema(type="boolean", example="true"),
     *      ),
     *      @OA\Response(
     *          response="200"
     *     )
     * )
     * @Route(name="organization-api.organization.merchant.set-active", path="/organization-api/v{version}/organization/merchant/{merchantId}/set-active", methods={"PATCH"})
     */
    public function setActiveMerchant(RequestDataBag $requestDataBag, OrganizationEntity $organizationEntity, string $merchantId): JsonResponse
    {
        if (!$requestDataBag->has('active')) {
            throw new \InvalidArgumentException('\'active\' parameter missing');
        }

        $active = $requestDataBag->get('active');

        $criteria = new Criteria([$organizationEntity->getId()]);
        $criteria->addAssociation('salesChannel.merchants');
        $criteria->addFilter(new EqualsFilter('salesChannel.merchants.id', $merchantId));

        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$organization) {
            throw new MerchantForOrganizationNotFoundException(sprintf('No merchant with ID \'%s\' found for current organization', $merchantId));
        }

        $this->merchantRepository->update([[
            'id' => $merchantId,
            'active' => $active
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }
}
