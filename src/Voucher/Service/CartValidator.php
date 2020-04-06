<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Service;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Error\IncompleteLineItemError;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Voucher\Exception\CartContainsMultipleProductTypes;

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

    public function validate(
        Cart $cart,
        ErrorCollection $errorCollection,
        SalesChannelContext $salesChannelContext
    ): void {
        $ids = array_filter($cart->getLineItems()->getReferenceIds());

        if (count($ids) === 0) {
            return;
        }

        $criteria = new Criteria($ids);

        $products = $this->productRepository->search($criteria, $salesChannelContext->getContext());

        $productTypes = [];

        foreach ($cart->getLineItems()->getFlat() as $lineItem) {
            $product = $products->get($lineItem->getReferencedId());
            if (!$product) {
                $errorCollection->add(new IncompleteLineItemError($lineItem->getId(), 'productId'));
                $cart->getLineItems()->removeElement($lineItem);
                continue;
            }

            $customFields = $product->getTranslation('customFields');
            if (isset($customFields['productType'])) {
                $productTypes[] = $customFields['productType'];
            }

            if (count(array_unique($productTypes)) > 1) {
                $errorCollection->add(new CartContainsMultipleProductTypes($lineItem->getId()));
                $cart->getLineItems()->removeElement($lineItem);
            }
        }
    }
}
