<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Checkout\Cart\Validator;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Error\IncompleteLineItemError;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartContainsMultipleMerchants;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartContainsStoreWindowLineItem;
use Shopware\Production\Merchants\Content\Merchant\Exception\CartInvalidShippingMethod;
use Shopware\Production\Merchants\Content\Merchant\MerchantCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Events\BlockShippingMethodsEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CartValidator implements CartValidatorInterface
{
    private const NOT_ORDER_ABLE_TYPE = 'storeWindow';

    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityRepositoryInterface $productRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->productRepository = $productRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function validate(Cart $cart, ErrorCollection $errorCollection, SalesChannelContext $salesChannelContext): void
    {
        $ids = array_filter($cart->getLineItems()->getReferenceIds());

        if (\count($ids) === 0) {
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

            if (!$this->isOrderAble($product)) {
                $errorCollection->add(new CartContainsStoreWindowLineItem($lineItem->getId()));
                $cart->getLineItems()->removeElement($lineItem);
                continue;
            }

            /** @var MerchantCollection|null $merchants */
            $merchants = $product->getExtension('merchants');
            if ($merchants === null) {
                continue;
            }

            $merchant = $merchants->first();
            if ($merchant === null) {
                continue;
            }

            $merchantIds[$merchant->getId()] = true;

            if (\count($merchantIds) > 1) {
                $errorCollection->add(new CartContainsMultipleMerchants($lineItem->getId()));
                $cart->getLineItems()->removeElement($lineItem);
            } else {
                $delivery = $cart->getDeliveries()->first();
                if ($delivery === null) {
                    continue;
                }

                $shippingMethods = new ShippingMethodCollection([$delivery->getShippingMethod()]);
                $event = new BlockShippingMethodsEvent($shippingMethods, $merchant, $salesChannelContext);
                $this->eventDispatcher->dispatch($event);

                if ($event->getShippingMethodCollection()->count() === 0) {
                    $errorCollection->add(new CartInvalidShippingMethod());
                    return;
                }


                $shippingMethodId = $delivery->getShippingMethod()->getId();
                $this->blockOrderWithInvalidShippingMethod(
                    $shippingMethodId,
                    $errorCollection,
                    $merchant,
                    $salesChannelContext
                );
            }
        }
    }

    private function isOrderAble(ProductEntity $productEntity): bool
    {
        return $productEntity->getTranslation('customFields')['productType'] !== self::NOT_ORDER_ABLE_TYPE;
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
