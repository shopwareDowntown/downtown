<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPage;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;

class ConfirmPageLoadedSubscriber
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(CheckoutConfirmPageLoadedEvent $event): void
    {
        $merchant = $this->getMerchant($event->getPage(), $event->getSalesChannelContext());
        if ($merchant === null) {
            return;
        }

        $this->filterShippingMethods($merchant, $event->getPage()->getShippingMethods(), $event->getSalesChannelContext());
    }

    private function getMerchant(CheckoutConfirmPage $page, SalesChannelContext $context): ?MerchantEntity
    {
        $productLineItem = $page->getCart()->getLineItems()->first();
        if ($productLineItem === null) {
            return null;
        }

        $productId = $productLineItem->getReferencedId();

        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('merchants.shippingMethods');

        $product = $this->productRepository->search($criteria, $context->getContext())->first();
        if ($product === null) {
            return null;
        }

        /** @var MerchantCollection|null $merchants */
        $merchants = $product->getExtension('merchants');
        if ($merchants === null || \count($merchants) <= 0) {
            return null;
        }

        return $merchants->first();
    }

    private function filterShippingMethods(MerchantEntity $merchant, ShippingMethodCollection $shippingMethods, SalesChannelContext $context): void
    {
        foreach ($shippingMethods as $id => $shippingMethod) {
            // The merchant cannot block the default shipping method
            if ($id === $context->getSalesChannel()->getShippingMethodId()) {
                continue;
            }

            if ($merchant->getShippingMethods()->has($id)) {
                continue;
            }

            $shippingMethods->remove($id);
        }
    }
}
