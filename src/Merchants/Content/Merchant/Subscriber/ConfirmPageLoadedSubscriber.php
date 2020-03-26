<?php

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
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

    public function __invoke(CheckoutConfirmPageLoadedEvent $event)
    {
        $merchant = $this->getMerchant($event->getPage(), $event->getSalesChannelContext());

        $this->filterShippingMethods($merchant, $event->getPage()->getShippingMethods(), $event->getSalesChannelContext());
    }

    private function getMerchant(CheckoutConfirmPage $page, SalesChannelContext $context): MerchantEntity
    {
        $productId = $page->getCart()->getLineItems()->first()->getReferencedId();

        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('merchants.shippingMethods');

        return $this->productRepository->search($criteria, $context->getContext())->first()->getExtension('merchants')->first();
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
