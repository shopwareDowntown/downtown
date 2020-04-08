<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Checkout\Cart\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Production\Merchants\Content\Merchant\MerchantCollection;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Twig\Environment;

class OrderPlacedSubscriber
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var TemplateMailSender
     */
    private $templateMailSender;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $orderRepository,
        TemplateMailSender $templateMailSender
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->templateMailSender = $templateMailSender;
    }

    public function __invoke(CheckoutOrderPlacedEvent $orderPlacedEvent): void
    {
        $orderLineItemCollection = $orderPlacedEvent->getOrder()->getLineItems();
        if ($orderLineItemCollection === null) {
            return;
        }

        $firstLineItem = $orderLineItemCollection->first();
        if ($firstLineItem === null) {
            return;
        }

        $productId = $firstLineItem->getProductId();

        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('merchants');

        /** @var ProductEntity $product */
        $product = $this->productRepository->search($criteria, $orderPlacedEvent->getContext())->first();

        /** @var MerchantCollection|null $merchants */
        $merchants = $product->getExtension('merchants');
        if ($merchants === null) {
            return;
        }

        $merchant = $merchants->first();
        if ($merchant === null) {
            return;
        }

        $this->orderRepository->update([
            [
                'id' => $orderPlacedEvent->getOrder()->getId(),
                'merchants' => [
                    [
                        'id' => $merchant->getId(),
                    ]
                ]
            ]
        ], $orderPlacedEvent->getContext());

        $this->templateMailSender->sendMail($merchant->getEmail(), 'merchant_order_confirmation', [
            'merchant' => $merchant,
            'order' => $orderPlacedEvent->getOrder()
        ]);
    }
}
