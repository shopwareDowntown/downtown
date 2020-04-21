<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Symfony\Component\HttpFoundation\Request;

class SalesChannelCmsPageLoader implements SalesChannelCmsPageLoaderInterface
{
    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $coreService;

    /**
     * @var CmsSlotsDataResolver
     */
    private $cmsSlotsDataResolver;

    public function __construct(SalesChannelCmsPageLoaderInterface $coreService, CmsSlotsDataResolver $cmsSlotsDataResolver)
    {
        $this->coreService = $coreService;
        $this->cmsSlotsDataResolver = $cmsSlotsDataResolver;
    }

    public function load(Request $request, Criteria $criteria, SalesChannelContext $context, ?array $config = null, ?ResolverContext $resolverContext = null): EntitySearchResult
    {
        $navigationId = $request->attributes->get('navigationId', $context->getSalesChannel()->getNavigationCategoryId());

        /** @var OrganizationEntity|null $organization */
        $organization = $context->getSalesChannel()->getExtension('organization');
        $cmsPageId = $request->attributes->get('id');

        if ($organization && $cmsPageId === null && $navigationId === $context->getSalesChannel()->getNavigationCategoryId()) {
            return $this->loadStartPage($request, $criteria, $context, $resolverContext, $organization);
        }

        return $this->coreService->load($request, $criteria, $context, $config, $resolverContext);
    }

    private function loadStartPage(Request $request, Criteria $criteria, SalesChannelContext $context, ?ResolverContext $resolverContext, OrganizationEntity $organization): EntitySearchResult
    {
        if (!$resolverContext) {
            $resolverContext = new ResolverContext($context, $request);
        }

        $id = $criteria->getIds()[0];

        $cmsPage = new CmsPageEntity();
        $cmsPage->setId(Uuid::randomHex());
        $cmsPage->setSections(new CmsSectionCollection());

        if ($organization->getHomeHeroImage()) {
            $this->addHeroImage($cmsPage, $organization);
        }

        if ($organization->getHomeText()) {
            $this->addText($cmsPage, $organization, $context->getSalesChannel());
        }

        $this->addMerchantListing($cmsPage);

        $slots = $this->cmsSlotsDataResolver->resolve($cmsPage->getSections()->last()->getBlocks()->getSlots(), $resolverContext);
        $cmsPage->getSections()->last()->getBlocks()->setSlots($slots);

        $result = new EntitySearchResult(1, new EntityCollection(), null,  $criteria, $context->getContext());
        $result->set($id, $cmsPage);

        return $result;
    }

    private function addHeroImage(CmsPageEntity $cmsPage, OrganizationEntity $organization): void
    {
        $imageStruct = new ImageStruct();
        $imageStruct->setMedia($organization->getHomeHeroImage());
        $imageStruct->setMediaId($organization->getHomeHeroImageId());

        $slot = new CmsSlotEntity();
        $slot->setId(Uuid::randomHex());
        $slot->setSlot('image');
        $slot->setType('image');
        $slot->setData($imageStruct);

        $block = new CmsBlockEntity();
        $block->setId(Uuid::randomHex());
        $block->setPosition(1);
        $block->setType('image');
        $block->setSectionPosition('main');
        $block->setSlots(new CmsSlotCollection([$slot]));
        $block->setMarginBottom('20px');

        $cmsSection = new CmsSectionEntity();
        $cmsSection->setId(Uuid::randomHex());
        $cmsSection->setBlocks(new CmsBlockCollection([$block]));
        $cmsSection->setPosition(1);
        $cmsSection->setType('default');

        $cmsPage->getSections()->add($cmsSection);
    }

    private function addText(CmsPageEntity $cmsPage, OrganizationEntity $organization, SalesChannelEntity $salesChannel): void
    {
        $textStructure = new TextStruct();
        $textStructure->setContent(sprintf('<h2 style="text-align: center;">%s</h2>
                                                <hr>
                                                <p style="text-align: center;">%s</p>',
                $salesChannel->getTranslation('name'),
                nl2br($organization->getHomeText()))
        );

        $slot = new CmsSlotEntity();
        $slot->setId(Uuid::randomHex());
        $slot->setSlot('content');
        $slot->setType('text');
        $slot->setData($textStructure);

        $block = new CmsBlockEntity();
        $block->setId(Uuid::randomHex());
        $block->setPosition(1);
        $block->setType('text-hero');
        $block->setSectionPosition('main');
        $block->setSlots(new CmsSlotCollection([$slot]));

        $cmsSection = new CmsSectionEntity();
        $cmsSection->setId(Uuid::randomHex());
        $cmsSection->setBlocks(new CmsBlockCollection([$block]));
        $cmsSection->setPosition(1);
        $cmsSection->setType('default');

        $cmsPage->getSections()->add($cmsSection);
    }

    private function addMerchantListing(CmsPageEntity $cmsPage): void
    {
        $slot = new CmsSlotEntity();
        $slot->setId(Uuid::randomHex());
        $slot->setSlot('content');
        $slot->setType('merchant-listing');

        $block = new CmsBlockEntity();
        $block->setId(Uuid::randomHex());
        $block->setPosition(1);
        $block->setType('merchant-listing');
        $block->setSectionPosition('main');
        $block->setSlots(new CmsSlotCollection([$slot]));
        $block->setMarginTop('20px');

        $cmsSection = new CmsSectionEntity();
        $cmsSection->setId(Uuid::randomHex());
        $cmsSection->setBlocks(new CmsBlockCollection([$block]));
        $cmsSection->setPosition(1);
        $cmsSection->setType('default');

        $cmsPage->getSections()->add($cmsSection);
    }
}
