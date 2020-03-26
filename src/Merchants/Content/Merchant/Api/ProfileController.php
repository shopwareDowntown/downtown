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
     * @Route(name="merchant-api.profile.load", path="/merchant-api/profile")
     */
    public function profile(SalesChannelContext $salesChannelContext): JsonResponse
    {
        $merchant = SalesChannelContextExtension::extract($salesChannelContext);

        return new JsonResponse($merchant);
    }

//    /**
//     * @Route(name="merchant-api.profile.save", methods={"PATCH"}, path="/merchant-api/profile")
//     */
//    public function save(DataBag $dataBag, SalesChannelContext $salesChannelContext): JsonResponse
//    {
//        $merchant = SalesChannelContextExtension::extract($salesChannelContext);
//
//        $df = (new DataValidationDefinition())
//            ->add('public', new NotBlank(), new Type('bool'))
//            ->add('name', new Type('string'))
//            ->add('email', new Type('string'))
//            ->add('password', new Type('string'))
//
//            ->add('website', new NotBlank(), new Type('string'))
//            ->add('description', new NotBlank(), new Type('string'))
//            ->add('phoneNumber', new NotBlank(), new Type('string'))
//
//            ->add('categoryId', new NotBlank(), new Uuid(), new EntityExists(['entity' => 'category', 'context' => $salesChannelContext->getContext()]));
//
//        $this->merchantRepository->update([[
//            $dataBag->only(... array_keys($df->getProperties()))
//        ]], $salesChannelContext->getContext());
//
//        $merchant = $this->merchantRepository
//            ->search(new Criteria([$merchant->getId()]), $salesChannelContext->getContext())
//            ->first();
//
//
//        return new JsonResponse($merchant);
//    }
}
