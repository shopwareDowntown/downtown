<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Page\Navigation;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Production\Merchants\System\SalesChannel\SalesChannelLandingPageEntity;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;

class NavigationPageSubscriber
{
    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $cmsPageLoader;

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(SalesChannelCmsPageLoaderInterface $cmsPageLoader, EntityRepositoryInterface $categoryRepository)
    {
        $this->cmsPageLoader = $cmsPageLoader;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws PageNotFoundException
     */
    public function __invoke(NavigationPageLoadedEvent $event)
    {
        $request = $event->getRequest();
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannel = $salesChannelContext->getSalesChannel();
        $landingPage = $salesChannel->getExtension('landingPage');
        $page = $event->getPage();

        if ($request->attributes->get('_route') !== 'frontend.home.page') {
            return;
        }

        if (!$landingPage instanceof SalesChannelLandingPageEntity) {
            return;
        }

        $navigationCategory = $this->loadNavigationCategory($salesChannel->getNavigationCategoryId(), $salesChannelContext->getContext());
        if ($navigationCategory === null) {
            return;
        }

        $resolverContext = new EntityResolverContext($salesChannelContext, $request, new CategoryDefinition(), $navigationCategory);

        $pageId = $landingPage->getCmsPageId();
        $pages = $this->cmsPageLoader->load(
            $request,
            new Criteria([$pageId]),
            $salesChannelContext,
            $navigationCategory->getTranslation('slotConfig'),
            $resolverContext
        );

        if (!$pages->has($pageId)) {
            throw new PageNotFoundException($pageId);
        }

        $page->setCmsPage($pages->get($pageId));

        $salesChannelName = $salesChannel->getTranslation('name');
        if ($salesChannelName === null || !\is_string($salesChannelName)) {
            return;
        }

        $metaInformation = $page->getMetaInformation();
        if ($metaInformation === null) {
            return;
        }

        $metaInformation->setMetaTitle($salesChannelName);
    }

    private function loadNavigationCategory(string $navigationCategoryId, Context $context): ?CategoryEntity
    {
        return $this->categoryRepository->search(new Criteria([$navigationCategoryId]), $context)->first();
    }
}