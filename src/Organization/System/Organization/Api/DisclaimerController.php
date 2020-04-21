<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Production\Portal\Hacks\StorefrontMediaUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class DisclaimerController
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
     *      path="/organization/disclaimer/image",
     *      description="Upload image for disclaimer",
     *      operationId="uploadDisclaimerImage",
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
     * @Route(name="organization-api.organization.disclaimer.image.upload", path="/organization-api/v{version}/organization/disclaimer/image", methods={"POST"})
     */
    public function uploadImage(Request $request, OrganizationEntity $organizationEntity, SalesChannelContext $context): JsonResponse
    {
        if (!$request->files->has('image')) {
            throw new \InvalidArgumentException('Parameter \'image\' missing.');
        }

        $mediaId = $this->storefrontMediaUploader->upload($request->files->get('image'), 'organization', 'organization_images', $context->getContext());

        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'disclaimer' => [
                'id' => $organizationEntity->getDisclaimer() ? $organizationEntity->getDisclaimer()->getId() : Uuid::randomHex(),
                'imageId' => $mediaId
            ]
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/organization/disclaimer/image",
     *      description="Remove disclaimer image",
     *      operationId="removeDisclaimerImage",
     *      tags={"Organization"},
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.organization.disclaimer.image.delete", path="/organization-api/v{version}/organization/disclaimer/image", methods={"DELETE"})
     */
    public function deleteMedia(OrganizationEntity $organizationEntity): JsonResponse
    {
        $this->organizationRepository->update([[
            'id' => $organizationEntity->getId(),
            'disclaimer' => [
                'id' => $organizationEntity->getDisclaimer() ? $organizationEntity->getDisclaimer()->getId() : Uuid::randomHex(),
                'imageId' => null
            ]
        ]], Context::createDefaultContext());

        return new JsonResponse([
            'success' => true
        ]);
    }
}
