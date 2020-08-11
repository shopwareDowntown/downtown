<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantAvailableFilter;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Events\MerchantPageCriteriaEvent;
use Shopware\Production\Merchants\Storefront\Page\MerchantPage;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MerchantController extends StorefrontController
{
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        SalesChannelRepositoryInterface $productRepository,
        EntityRepositoryInterface $merchantRepository,
        GenericPageLoader $genericPageLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->productRepository = $productRepository;
        $this->merchantRepository = $merchantRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->eventDispatcher = $eventDispatcher;
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

        return $this->renderStorefront('storefront/page/merchant/detail.html.twig', $vars);
    }

    private function loadMerchant(string $id, SalesChannelContext $context): MerchantEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('products');
        $criteria->addAssociation('cover.thumbnails');
        $criteria->addAssociation('country');
        $criteria->addAssociation('services');
        $criteria->addFilter(new MerchantAvailableFilter($context->getSalesChannel()->getId()));
        $this->eventDispatcher->dispatch(new MerchantPageCriteriaEvent($criteria));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, $context->getContext())->first();
        if ($merchant === null) {
            throw new NotFoundHttpException(sprintf('Couldn\'t find merchant by id %s', $id));
        }

        $productCollection = $merchant->getProducts();
        if ($productCollection === null) {
            throw new NotFoundHttpException(
                sprintf('Couldn\'t find any products for the merchant with the id "%s"', $merchant->getId())
            );
        }

        $productIds = $productCollection->getIds();
        if (\count($productIds)) {
            $merchant->setProducts($this->productRepository->search(new Criteria($productIds), $context)->getEntities());
        }

        return $merchant;
    }
}
