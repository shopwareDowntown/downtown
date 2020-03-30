<?php

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Content\Media\Exception\UploadException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;
use Shopware\Production\Portal\Hacks\StorefrontMediaUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @RouteScope(scopes={"storefront"})
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

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        EntityRepositoryInterface $merchantMediaRepository,
        DataValidator $dataValidator,
        StorefrontMediaUploader $uploader
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->merchantMediaRepository = $merchantMediaRepository;
        $this->dataValidator = $dataValidator;
        $this->uploader = $uploader;
    }

    /**
     * @Route(name="merchant-api.profile.load", methods={"GET"}, path="/merchant-api/v{version}/profile")
     */
    public function profile(SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        $profileData = $this->fetchProfileData($salesChannelContext, $merchant);

        return new JsonResponse($profileData);
    }

    /**
     * @Route(name="merchant-api.profile.save", methods={"PATCH"}, path="/merchant-api/v{version}/profile")
     */
    public function save(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        $merchantConstraints = $this->createValidationDefinition($salesChannelContext);

        $this->dataValidator->validate($dataBag->all(), $merchantConstraints);

        $this->merchantRepository->update([
            array_merge(
                ['id' => $merchant->getId()],
                $dataBag->only(... array_keys($merchantConstraints->getProperties()))
            )
        ], $salesChannelContext->getContext());

        return new JsonResponse($this->fetchProfileData($salesChannelContext, $merchant));
    }

    /**
     * @Route(name="merchant-api.profile.image.save", methods={"POST"}, path="/merchant-api/v{version}/profile/media")
     */
    public function upload(Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        $uploadedMedia = [];
        $cover = [];

        foreach ($request->files as $name => $upload) {
            try {
                $mediaId = $this->uploader->upload($upload, 'merchants', 'images', $salesChannelContext->getContext());
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
     * @Route(name="merchant-api.profile.image.delete", methods={"DELETE"}, path="/merchant-api/v{version}/profile/media/:mediaId")
     */
    public function delete(string $mediaId, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        $updateSet = [
            'mediaId' => $mediaId,
            'merchantId' => $merchant->getId()
        ];

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
            ->add('country', new Type('string'))
            ->add('email', new Type('string'))
            ->add('password', new Type('string'))
            ->add('phoneNumber', new Type('string'));
    }

    protected function fetchProfileData(SalesChannelContext $salesChannelContext, MerchantEntity $merchant): array
    {
        $criteria = new Criteria([$merchant->getId()]);
        $criteria->addAssociation('media.thumbnails');
        $criteria->addAssociation('cover');

        $profile = $this->merchantRepository->search($criteria, $salesChannelContext->getContext())->first();

        $profileData = json_decode(json_encode($profile), true);

        unset($profileData['password'], $profileData['extensions']);
        unset($profileData['_uniqueIdentifier']);

        return $profileData;
    }
}
