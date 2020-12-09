<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Storefront\Controller;

use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class StaticPageController extends StorefrontController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var GenericPageLoader
     */
    private $genericPageLoader;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(
        EntityRepositoryInterface $organizationRepository,
        GenericPageLoader $genericPageLoader,
        Translator $translator
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->translator = $translator;
    }

    /**
     * @Route(path="/imprint", name="organization.imprint", defaults={"page"="imprint"}, methods={"GET"})
     * @Route(path="/tos", name="organization.tos", defaults={"page"="tos"}, methods={"GET"})
     * @Route(path="/privacy", name="organization.privacy", defaults={"page"="privacy"}, methods={"GET"})
     */
    public function page(Request $request, SalesChannelContext $context): Response
    {
        $page = $this->genericPageLoader->load($request, $context);
        $page = NavigationPage::createFrom($page);

        $organization = $this->fetchOrganization($context->getSalesChannel()->getId());
        $this->loadMetaData($page, $request->attributes->get('page'));
        $this->addCmsPage($organization, $page, $request->attributes->get('page'));

        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', ['page' => $page]);
    }

    public function fetchOrganization(string $salesChannelId): OrganizationEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $criteria->addAssociation('salesChannel');

        return $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();
    }

    private function loadMetaData(NavigationPage $page, string $field): void
    {
        $metaInformation = $page->getMetaInformation();

        $metaInformation->setMetaTitle($this->trans('page.' . $field));
    }

    private function addCmsPage(OrganizationEntity $organization, NavigationPage $page, string $field): void
    {
        $headline = $this->translator->trans("page.{$field}");

        $textStructure = new TextStruct();
        $textStructure->setContent(
            "<h2>{$headline}</h2>\n" . nl2br($organization->jsonSerialize()[$field] ?? '')
        );

        $slot = new CmsSlotEntity();
        $slot->setId(Uuid::randomHex());
        $slot->setSlot('content');
        $slot->setType('text');
        $slot->setData($textStructure);

        $block = new CmsBlockEntity();
        $block->setId(Uuid::randomHex());
        $block->setPosition(1);
        $block->setType('text');
        $block->setSectionPosition('main');
        $block->setSlots(new CmsSlotCollection([$slot]));

        $cmsSection = new CmsSectionEntity();
        $cmsSection->setId(Uuid::randomHex());
        $cmsSection->setBlocks(new CmsBlockCollection([$block]));
        $cmsSection->setPosition(1);
        $cmsSection->setType('default');

        $cmsPage = new CmsPageEntity();
        $cmsPage->setId(Uuid::randomHex());
        $cmsPage->setSections(new CmsSectionCollection([$cmsSection]));

        $page->setCmsPage($cmsPage);
    }
}
