<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Page\Navigation;

use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;

class NavigationPageSubscriber
{
    /**
     * @throws PageNotFoundException
     */
    public function __invoke(NavigationPageLoadedEvent $event)
    {
        $request = $event->getRequest();
        $salesChannelContext = $event->getSalesChannelContext();
        $salesChannel = $salesChannelContext->getSalesChannel();
        /** @var OrganizationEntity|null $organization */
        $organization = $salesChannel->getExtension('organization');
        $page = $event->getPage();

        if ($request->attributes->get('_route') !== 'frontend.home.page') {
            return;
        }

        if (!$organization) {
            return;
        }

        $page->setCmsPage($this->createCmsPage($organization, $salesChannel));

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

    private function createCmsPage(OrganizationEntity $organization, SalesChannelEntity $salesChannel): CmsPageEntity
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setId(Uuid::randomHex());
        $cmsPage->setSections(new CmsSectionCollection());

        if ($organization->getHomeHeroImage()) {
            $this->addHeroImage($cmsPage, $organization);
        }

        if ($organization->getHomeText()) {
            $this->addText($cmsPage, $organization, $salesChannel);
        }

        return $cmsPage;
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
}
