<?php

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartContainsMultipleMerchants;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartInvalidShippingMethod;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

class CartValidator implements CartValidatorInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function validate(Cart $cart, ErrorCollection $errorCollection, SalesChannelContext $salesChannelContext): void
    {
        $ids = array_filter($cart->getLineItems()->getReferenceIds());

        if (count($ids) === 0) {
            return;
        }

        $criteria = new Criteria($ids);

        $criteria->addAssociation('merchants.shippingMethods');

        $products = $this->productRepository->search($criteria, $salesChannelContext->getContext());

        $merchantIds = [];

        foreach ($cart->getLineItems()->getFlat() as $lineItem) {
            /** @var ProductEntity $product */
            $product = $products->get($lineItem->getReferencedId());

            if (!$product) {
                $errorCollection->add(new IncompleteLineItemError($lineItem->getId(), 'productId'));
                $cart->getLineItems()->removeElement($lineItem);
                continue;
            }

            $merchantId = $product->getExtension('merchants')->first()->getId();
            /** @var MerchantEntity $merchant */
            $merchant = $product->getExtension('merchants')->first();

            $merchantIds[$merchant->getId()] = true;

            if (count($merchantIds) > 1) {
                $errorCollection->add(new CartContainsMultipleMerchants($lineItem->getId()));
                $cart->getLineItems()->removeElement($lineItem);
            } else {
                $shippingMethodId = $cart->getDeliveries()->first()->getShippingMethod()->getId();
                $this->blockOrderWithInvalidShippingMethod($shippingMethodId, $errorCollection, $merchant, $salesChannelContext);
            }
        }
    }

    private function blockOrderWithInvalidShippingMethod(
        string $activeId,
        ErrorCollection $errorCollection,
        MerchantEntity $merchantEntity,
        SalesChannelContext $context
    ): void {
        // Default shipping method
        if ($activeId === $context->getSalesChannel()->getShippingMethodId()) {
            return;
        }

        if ($merchantEntity->getShippingMethods()->has($activeId)) {
            return;
        }

        $errorCollection->add(new CartInvalidShippingMethod());
    }
}
