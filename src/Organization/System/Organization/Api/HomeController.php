<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Production\Portal\Hacks\StorefrontMediaUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class HomeController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var StorefrontMediaUploader
     */
    private $storefrontMediaUploader;

    public function __construct(
        EntityRepositoryInterface $organizationRepository,
        StorefrontMediaUploader $storefrontMediaUploader
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->storefrontMediaUploader = $storefrontMediaUploader;
    }

    /**
     * @OA\Post(
     *      path="/organization/home/heroImage",
     *      description="Upload hero image for home page",
     *      operationId="uploadHeroImage",
     *      tags={"Organization"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="File to upload",
     *                     property="image",
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
     * @Route(name="organization-api.organization.upload-hero-image", path="/organization-api/v{version}/organization/home/heroImage", methods={"POST"})
     */
    public function uploadHeroImage(Request $request, OrganizationEntity $organizationEntity, SalesChannelContext $context): JsonResponse
    {
        if (!$request->files->has('image')) {
            throw new \InvalidArgumentException('Parameter \'image\' missing.');
        }

        $mediaId = $this->storefrontMediaUploader->upload($request->files->get('image'), 'organization', 'organization_images', $context->getContext());

        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'homeHeroImageId' => $mediaId
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/organization/home/heroImage",
     *      description="Remove hero image",
     *      operationId="removeHeroImage",
     *      tags={"Organization"},
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.organization.remove-hero-image", path="/organization-api/v{version}/organization/home/heroImage", methods={"DELETE"})
     */
    public function deleteMedia(OrganizationEntity $organizationEntity): JsonResponse
    {
        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'homeHeroImageId' => null
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }
}
