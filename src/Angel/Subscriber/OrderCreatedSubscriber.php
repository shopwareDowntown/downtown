<?php declare(strict_types=1);

namespace Shopware\Production\Angel\Subscriber;

use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_transaction.state.paid' => 'onOrderTransactionChange',
        ];
    }

    public function onOrderTransactionChange(OrderStateMachineStateChangeEvent $event): void
    {
        // Order is paid, $event->getOrder() to access the order
    }
}
