<?php

namespace Shopware\Production\Merchants\Content\Merchant\Storefront\Controller;

use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\Storefront\Page\MerchantPage;
use Shopware\Production\Merchants\Content\Merchant\Storefront\Service\MerchantCriteriaLoaderInterface;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MerchantController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var SalesChannelRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var GenericPageLoader
     */
    private $genericPageLoader;

    /**
     * @var MerchantCriteriaLoaderInterface
     */
    private $criteriaLoader;

    public function __construct(
        Environment $twig,
        SalesChannelRepositoryInterface $productRepository,
        EntityRepositoryInterface $merchantRepository,
        GenericPageLoader $genericPageLoader,
        MerchantCriteriaLoaderInterface $criteriaLoader
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->merchantRepository = $merchantRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->criteriaLoader = $criteriaLoader;
    }

    /**
     * @Route(name="storefront.merchant.detail", path="/merchant/{id}")
     */
    public function detail(Request $request, string $id, SalesChannelContext $context): Response
    {
        $page = $this->genericPageLoader->load($request, $context);
        $page = MerchantPage::createFrom($page);
        $page->setMerchant($this->loadMerchant($id, $context));

        $vars = [
            'page' => $page
        ];

        $html = $this->twig->render('@Merchant/page/merchant/detail.html.twig', $vars);

        return new Response($html);
    }

    private function loadMerchant(string $id, SalesChannelContext $context): MerchantEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->addFilter(new EqualsFilter('salesChannelId', $context->getSalesChannel()->getId()));

        $criteria = $this->criteriaLoader->getMerchantCriteria($criteria);
        
        /** @var MerchantEntity $merchant */
        $merchant = $this->merchantRepository->search($criteria, $context->getContext())->first();

        if (!$merchant) {
            throw new NotFoundHttpException(sprintf('Cannot find merchant by id %s', $id));
        }

        $productIds = $merchant->getProducts()->getIds();

        if (count($productIds)) {
            $merchant->setProducts($this->productRepository->search(new Criteria($productIds), $context)->getEntities());
        }

        return $merchant;
    }
}
