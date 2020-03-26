<?php

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Error\IncompleteLineItemError;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartContainsMultipleMerchants;

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

        $criteria->addAssociation('merchants');

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

            $merchantIds[$merchantId] = true;

            if (count($merchantIds) > 1) {
                $errorCollection->add(new CartContainsMultipleMerchants($lineItem->getId()));
                $cart->getLineItems()->removeElement($lineItem);
            }
        }
    }
}
