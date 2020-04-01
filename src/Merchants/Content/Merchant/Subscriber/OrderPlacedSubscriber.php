<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Content\MailTemplate\Service\MailSender;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Production\Merchants\Content\Merchant\MerchantCollection;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
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
     * @var Environment
     */
    private $twig;

    /**
     * @var MailSender
     */
    private $mailService;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $orderRepository,
        Environment $twig,
        MailSender $mailService,
        SystemConfigService $systemConfigService
    ) {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->twig = $twig;
        $this->mailService = $mailService;
        $this->systemConfigService = $systemConfigService;
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

        $this->sendConfirmationMailToMerchant($merchant, $orderPlacedEvent);
    }

    private function sendConfirmationMailToMerchant(MerchantEntity $merchantEntity, CheckoutOrderPlacedEvent $orderPlacedEvent): void
    {
        $html = $this->twig->render('@Merchant/email/merchant_order_confirmation.html.twig', [
            'merchant' => $merchantEntity,
            'order' => $orderPlacedEvent->getOrder()
        ]);

        $senderEmail = $this->systemConfigService->get('core.basicInformation.email');

        $mail = new \Swift_Message('Neue Bestellung');
        $mail->addTo($merchantEntity->getEmail(), $merchantEntity->getPublicCompanyName());
        $mail->addFrom($senderEmail);
        $mail->setBody($html, 'text/html');

        $this->mailService->send($mail);
    }
}
