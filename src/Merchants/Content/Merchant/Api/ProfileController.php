<?php

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProfileController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;
    /**
     * @var DataValidator
     */
    private $dataValidator;

    public function __construct(EntityRepositoryInterface $merchantReepository, DataValidator $dataValidator)
    {
        $this->merchantRepository = $merchantReepository;
        $this->dataValidator = $dataValidator;
    }

    /**
     * @Route(name="merchant-api.profile.load", path="/merchant-api/v{version}/profile")
     */
    public function profile(SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        $merchant = json_decode(json_encode($merchant), true);

        unset($merchant['password']);
        unset($merchant['extensions']);
        unset($merchant['_uniqueIdentifier']);

        return new JsonResponse($merchant);
    }

    /**
     * @Route(name="merchant-api.profile.save", methods={"PATCH"}, path="/merchant-api/v{version}/profile")
     */
    public function save(DataBag $dataBag, SalesChannelContext $salesChannelContext): JsonResponse
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

        $merchant = $this->merchantRepository
            ->search(new Criteria([$merchant->getId()]), $salesChannelContext->getContext())
            ->first();

        return new JsonResponse($merchant);
    }

    protected function createValidationDefinition(SalesChannelContext $salesChannelContext): DataValidationDefinition
    {
        return (new DataValidationDefinition())
            ->add('public', new NotBlank(), new Type('bool'))
            ->add('publicCompanyName', new Type('string'))
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
}
