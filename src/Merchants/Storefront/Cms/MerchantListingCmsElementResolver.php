<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Production\Merchants\Content\Merchant\MerchantAvailableFilter;
use Shopware\Production\Merchants\Storefront\Listing\MerchantListingCriteriaEvent;
use Shopware\Production\Merchants\Storefront\Listing\MerchantListingResult;
use Shopware\Production\Merchants\Storefront\Listing\MerchantListingResultEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MerchantListingCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getType(): string
    {
        return 'merchant-listing';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new MerchantListingStruct();
        $slot->setData($data);

        $categoryId = $this->requestStack->getMasterRequest()->attributes->get('navigationId');
        $salesChannelId = $resolverContext->getSalesChannelContext()->getSalesChannel()->getId();

        $criteria = new Criteria();

        if ($categoryId && $categoryId !== $resolverContext->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId()) {
            $criteria->addFilter(new EqualsFilter('categoryId', $categoryId));
        }

        $criteria->addAssociation('services');
        $criteria->addAssociation('cover');
        $criteria->addFilter(new MerchantAvailableFilter($salesChannelId));
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        $salesChannelContext = $resolverContext->getSalesChannelContext();
        $request = $resolverContext->getRequest();

        $this->eventDispatcher->dispatch(
            new MerchantListingCriteriaEvent($request, $criteria, $salesChannelContext)
        );

        $listing = $this->merchantRepository->search($criteria, $salesChannelContext->getContext());
        $listing = MerchantListingResult::createFrom($listing);

        $this->eventDispatcher->dispatch(
            new MerchantListingResultEvent($request, $listing, $salesChannelContext)
        );

        $data->setListing($listing);
        $data->setNavigationId($request->attributes->get('navigationId', $resolverContext->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId()));
    }
}
