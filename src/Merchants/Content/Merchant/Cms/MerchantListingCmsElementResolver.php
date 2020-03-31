<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
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

    public function __construct(EntityRepositoryInterface $merchantRepository, RequestStack $requestStack)
    {
        $this->merchantRepository = $merchantRepository;
        $this->requestStack = $requestStack;
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
        $criteria->addFilter(new EqualsFilter('categoryId', $categoryId));
        $criteria->addFilter(new EqualsFilter('public', 1));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addAssociation('cover');
        $listing = $this->merchantRepository->search($criteria, $resolverContext->getSalesChannelContext()->getContext());

        $data->setListing($listing);
    }
}
