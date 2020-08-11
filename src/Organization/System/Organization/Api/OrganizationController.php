<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\Api\ResponseFields;
use Shopware\Core\System\SalesChannel\Api\StructEncoder;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Organization\Exception\MerchantForOrganizationNotFoundException;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Production\Portal\Hacks\StorefrontMediaUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Type;

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

    /**
     * @var DataValidator
     */
    private $dataValidator;

    /**
     * @var StorefrontMediaUploader
     */
    private $storefrontMediaUploader;

    /**
     * @var StructEncoder
     */
    private $structEncoder;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        EntityRepositoryInterface $organizationRepository,
        EntityRepositoryInterface $merchantRepository,
        DataValidator $dataValidator,
        StorefrontMediaUploader $storefrontMediaUploader,
        StructEncoder $structEncoder
    ) {
        $this->salesChannelRepository = $salesChannelRepository;
        $this->organizationRepository = $organizationRepository;
        $this->merchantRepository = $merchantRepository;
        $this->dataValidator = $dataValidator;
        $this->storefrontMediaUploader = $storefrontMediaUploader;
        $this->structEncoder = $structEncoder;
    }

    /**
     * @OA\Get(
     *      path="/organizations",
     *      description="Get all organizations",
     *      operationId="loadAllOrganization",
     *      tags={"Organization"},
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
                'domain' => $domainEntity->getUrl()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @OA\Get(
     *      path="/organization",
     *      description="Get logged in organization",
     *      operationId="loadOrganization",
     *      tags={"Organization"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/components/schemas/OrganizationEntity")
     *     )
     * )
     * @Route(name="organization-api.organization", path="/organization-api/v{version}/organization", methods={"GET"})
     */
    public function loadOne(OrganizationEntity $organizationEntity, SalesChannelContext $context): JsonResponse
    {
        $organization = $organizationEntity->jsonSerialize();
        $organization['name'] = $context->getSalesChannel()->getTranslation('name');

        return new JsonResponse($organization);
    }

    /**
     * @OA\Patch(
     *      path="/organization",
     *      description="Update information about the loggedin organization",
     *      operationId="saveOrganization",
     *      tags={"Organization"},
     *      @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/OrganizationEntity")),
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/components/schemas/OrganizationEntity")
     *     )
     * )
     * @Route(name="organization-api.organization.save", path="/organization-api/v{version}/organization", methods={"PATCH"})
     */
    public function save(RequestDataBag $dataBag, OrganizationEntity $organizationEntity): JsonResponse
    {
        $constraints = $this->createValidationDefinition();

        $this->dataValidator->validate($dataBag->all(), $constraints);
        $properties = array_keys($constraints->getProperties());
        $properties[] = 'disclaimer';
        $data = array_intersect_key($dataBag->all(), array_flip($properties));

        if (isset($data['name'])) {
            $data['salesChannel'] = [
                'id' => $organizationEntity->getSalesChannelId(),
                'name' => $data['name']
            ];
            unset($data['name']);
        }

        if ($organizationEntity->getDisclaimer()) {
            $data['disclaimer']['id'] = $organizationEntity->getDisclaimer()->getId();
        }

        $this->organizationRepository->update([
            array_merge(
                ['id' => $organizationEntity->getId()],
                $data
            )
        ], Context::createDefaultContext());

        $criteria = new Criteria([$organizationEntity->getId()]);
        $criteria->addAssociation('salesChannel');

        /** @var OrganizationEntity|null $organizationEntity */
        $organizationEntity = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        $organization = $organizationEntity->jsonSerialize();
        $organization['name'] = $organizationEntity->getSalesChannel()->getTranslation('name');
        unset($organization['salesChannel']);

        return new JsonResponse($organization);
    }

    /**
     * @OA\Post(
     *      path="/organization/logo",
     *      description="Upload logo",
     *      operationId="uploadLogo",
     *      tags={"Organization"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="File to upload",
     *                     property="logo",
     *                     type="string",
     *                     format="file",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.organization.upload-logo", path="/organization-api/v{version}/organization/logo", methods={"POST"})
     */
    public function uploadLogo(Request $request, OrganizationEntity $organizationEntity, SalesChannelContext $context): JsonResponse
    {
        if (!$request->files->has('logo')) {
            throw new \InvalidArgumentException('Parameter \'logo\' missing.');
        }

        $mediaId = $this->storefrontMediaUploader->upload($request->files->get('logo'), 'organization', 'organization_images', $context->getContext());

        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'logoId' => $mediaId
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/organization/logo",
     *      description="Remove logo",
     *      operationId="removeLogo",
     *      tags={"Organization"},
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.organization.remove-logo", path="/organization-api/v{version}/organization/logo", methods={"DELETE"})
     */
    public function deleteMedia(OrganizationEntity $organizationEntity): JsonResponse
    {
        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'logoId' => null
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @OA\Get(
     *      path="/organization/merchants",
     *      description="Get all merchants for organization",
     *      operationId="loadAllOrganizationMerchants",
     *      tags={"Organization"},
     *      @OA\Parameter(
     *        name="limit",
     *        in="body",
     *        description="Limit",
     *        @OA\Schema(type="integer", example="100"),
     *      ),
     *      @OA\Parameter(
     *        name="offset",
     *        in="body",
     *        description="Offset",
     *        @OA\Schema(type="integer", example="100"),
     *      ),
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
    public function getMerchants(Request $request, OrganizationEntity $organizationEntity): JsonResponse
    {
        $criteria = new Criteria([$organizationEntity->getId()]);
        $criteria->addAssociation('salesChannel.merchants');

        /** @var OrganizationEntity $organization */
        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        $salesChannelId = $organization->getSalesChannelId();

        $merchantCriteria = new Criteria();
        $merchantCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        if ($request->query->has('limit')) {
            $merchantCriteria->setLimit((int) $request->query->get('limit'));
        }

        if ($request->query->has('offset')) {
            $merchantCriteria->setOffset((int) $request->query->get('offset'));
        }

        $merchantCriteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        $merchantCriteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        $merchants = $this->merchantRepository->search($merchantCriteria, Context::createDefaultContext());

        return new JsonResponse([
            'data' => $this->structEncoder->encode($merchants->getEntities(), 3, new ResponseFields([])),
            'total' => $merchants->getTotal()
        ]);
    }

    /**
     * @OA\Patch(
     *      path="/organization/merchant/{merchantId}/set-active",
     *      description="Set merchant active state",
     *      operationId="merchantActive",
     *      tags={"Organization"},
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

    private function createValidationDefinition(): DataValidationDefinition
    {
        return (new DataValidationDefinition())
            ->add('name', new Type('string'))
            ->add('firstName', new Type('string'))
            ->add('lastName', new Type('string'))
            ->add('phone', new Type('string'))
            ->add('city', new Type('string'))
            ->add('postCode', new Type('string'))
            ->add('imprint', new Type('string'))
            ->add('tos', new Type('string'))
            ->add('privacy', new Type('string'))
            ->add('homeText', new Type('string'))
            ->addSub('disclaimer', (new DataValidationDefinition())
                ->add('active', new Type('boolean'))
                ->add('text', new Type('string'))
            );
    }
}
