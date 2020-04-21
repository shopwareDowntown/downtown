<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Content\Media\Exception\UploadException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Portal\Hacks\StorefrontMediaUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class ProfileController
{
    private const COVER_UPLOAD_NAME = 'cover';

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
    private $uploader;
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantMediaRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantServiceRepository;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        EntityRepositoryInterface $merchantMediaRepository,
        EntityRepositoryInterface $merchantServiceRepository,
        DataValidator $dataValidator,
        StorefrontMediaUploader $uploader
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->merchantMediaRepository = $merchantMediaRepository;
        $this->merchantServiceRepository = $merchantServiceRepository;
        $this->dataValidator = $dataValidator;
        $this->uploader = $uploader;
    }

    /**
     * @OA\Get(
     *      path="/profile",
     *      description="Get profile",
     *      operationId="profile",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/components/schemas/MerchantEntity")
     *     )
     * )
     * @Route(name="merchant-api.profile.load", methods={"GET"}, path="/merchant-api/v{version}/profile")
     */
    public function profile(MerchantEntity $merchant, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $profileData = $this->fetchProfileData($salesChannelContext, $merchant);

        return new JsonResponse($profileData);
    }

    /**
     * @OA\Patch(
     *      path="/profile",
     *      description="Update profile",
     *      operationId="updateProfile",
     *      tags={"Merchant"},
     *      @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/MerchantEntity")),
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/components/schemas/MerchantEntity")
     *     )
     * )
     * @Route(name="merchant-api.profile.save", methods={"PATCH"}, path="/merchant-api/v{version}/profile")
     */
    public function save(Request $request, RequestDataBag $dataBag, MerchantEntity $merchant, SalesChannelContext $salesChannelContext): JsonResponse
    {
        if ($dataBag->has('country')) {
            $dataBag->set('countryId', $dataBag->get('country'));
            $dataBag->remove('country');
        }

        if ($dataBag->has('publicWebsite')) {
            $scheme = parse_url($dataBag->get('publicWebsite'), PHP_URL_SCHEME);

            if ($scheme === null) {
                $dataBag->set('publicWebsite', 'https://' . $dataBag->get('publicWebsite'));
            }
        }

        $merchantConstraints = $this->createValidationDefinition($salesChannelContext);
        $properties = array_keys($merchantConstraints->getProperties());
        $properties[] = 'services';

        $this->dataValidator->validate($dataBag->all(), $merchantConstraints);

        $this->merchantRepository->update([
            array_merge(
                ['id' => $merchant->getId()],
                array_intersect_key($dataBag->all(), array_flip($properties))
            )
        ], $salesChannelContext->getContext());

        if ($dataBag->has('services')) {
            $this->cleanupServices($merchant, array_column($dataBag->all()['services'], 'id'));
        }

        return new JsonResponse($this->fetchProfileData($salesChannelContext, $merchant));
    }

    /**
     * @OA\Post(
     *      path="/profile/media",
     *      description="Profile media Upload",
     *      operationId="profileMedia",
     *      tags={"Merchant"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="File to upload",
     *                     property="cover",
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
     * @Route(name="merchant-api.profile.image.save", methods={"POST"}, path="/merchant-api/v{version}/profile/media", defaults={"csrf_protected"=false})
     */
    public function upload(MerchantEntity $merchant, Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $uploadedMedia = [];
        $cover = [];

        foreach ($request->files as $name => $upload) {
            try {
                $mediaId = $this->uploader->upload($upload, 'merchants', 'merchant_images', $salesChannelContext->getContext());
            } catch (UploadException $e) {
                continue;
            }

            $uploadedMedia[] = ['id' => $mediaId];

            if ($name === self::COVER_UPLOAD_NAME) {
                $cover = ['coverId' => $mediaId];
            }
        }

        $additionalMediaAssociations = [
            'id' => $merchant->getId(),
            'media' => $uploadedMedia,
        ];

        $this->merchantRepository
            ->update(
                [array_merge($additionalMediaAssociations, $cover)],
                $salesChannelContext->getContext()
            );

        return new JsonResponse(true);
    }

    /**
     * @OA\Delete(
     *      path="/profile/media/{mediaId}",
     *      description="Delete profile media",
     *      operationId="deleteProfileMedia",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          ref="#/definitions/SuccessResponse"
     *     )
     * )
     * @Route(name="merchant-api.profile.image.delete", methods={"DELETE"}, path="/merchant-api/v{version}/profile/media/{mediaId}")
     */
    public function deleteMedia(string $mediaId, MerchantEntity $merchant, SalesChannelContext $salesChannelContext): JsonResponse
    {
        if ($mediaId === $merchant->getCoverId()) {
            $this->merchantRepository
                ->update([[
                    'id' => $merchant->getId(),
                    'coverId' => null,
                ]], $salesChannelContext->getContext());
        }

        $this->merchantMediaRepository
            ->delete([[
                'mediaId' => $mediaId,
                'merchantId' => $merchant->getId()
            ]], $salesChannelContext->getContext());

        return new JsonResponse([]);
    }

    protected function createValidationDefinition(SalesChannelContext $salesChannelContext): DataValidationDefinition
    {
        return (new DataValidationDefinition())
            ->add('public', new Type('bool'))
            ->add('publicCompanyName', new Type('string'))
            ->add('publicOwner', new Type('string'))
            ->add('publicPhoneNumber', new Type('string'))
            ->add('publicEmail', new Type('string'))
            ->add('publicOpeningTimes', new Type('string'))
            ->add('publicDescription', new Type('string'))
            ->add('publicWebsite', new Type('string'))
            ->add('categoryId', new EntityExists(['entity' => 'category', 'context' => $salesChannelContext->getContext()]))

            ->add('firstName', new Type('string'))
            ->add('lastName', new Type('string'))
            ->add('street', new Type('string'))
            ->add('zip', new Type('string'))
            ->add('city', new Type('string'))
            ->add('countryId', new EntityExists(['entity' => 'country', 'context' => $salesChannelContext->getContext()]))
            ->add('email', new Email())
            ->add('password', new Length(['min' => 8]))
            ->add('phoneNumber', new Type('string'))
            ->add('tos', new Type('string'))
            ->add('privacy', new Type('string'))
            ->add('imprint', new Type('string'))
            ->add('revocation', new Type('string'))
            ->add('availability', new Type('integer'))
            ->add('availabilityText', new Type('string'))
            ->add('mollieProdKey', new Type('string'))
            ->add('mollieTestKey', new Type('string'))
            ->add('mollieTestEnabled', new Type('bool'))
            ->add('paymentMethods', new Type('string'))
            ->addList('services', (new DataValidationDefinition())->add('id', new Type('string')));
    }

    protected function fetchProfileData(SalesChannelContext $salesChannelContext, MerchantEntity $merchant): array
    {
        $criteria = new Criteria([$merchant->getId()]);
        $criteria->addAssociation('media.thumbnails');
        $criteria->addAssociation('cover');
        $criteria->addAssociation('services');

        $profile = $this->merchantRepository->search($criteria, $salesChannelContext->getContext())->first();

        $profileData = json_decode(json_encode($profile), true);

        unset($profileData['password'], $profileData['extensions'], $profileData['_uniqueIdentifier']);

        return $profileData;
    }

    private function cleanupServices(MerchantEntity $merchant, array $newIds): void
    {
        $criteria = new Criteria([$merchant->getId()]);
        $criteria->addAssociation('services');
        $merchant = $this->merchantRepository->search($criteria, Context::createDefaultContext())->first();

        $idsToBeDeleted = [];

        foreach ($merchant->getServices() as $service) {
            if (!in_array($service->getId(), $newIds, true)) {
                $idsToBeDeleted[] = $service->getId();
            }
        }

        if (!count($idsToBeDeleted)) {
            return;
        }

        $this->merchantServiceRepository->delete(array_map(static function (string $id) use($merchant) {
            return [
                'merchantId' => $merchant->getId(),
                'serviceId' => $id
            ];
        }, $idsToBeDeleted), Context::createDefaultContext());
    }
}
