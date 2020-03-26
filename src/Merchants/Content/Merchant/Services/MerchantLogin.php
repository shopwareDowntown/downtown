<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MerchantLogin implements EventSubscriberInterface
{

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(EntityRepositoryInterface $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            CustomerLoginEvent::class => 'hijackLogin'
        ];
    }

    public function hijackLogin(CustomerLoginEvent $customerLoginEvent): void
    {
        $merchant = $this->fetchMerchant($customerLoginEvent);

        if($merchant === null) {
            return;
        }

        $this->

        dump($customerLoginEvent);

        $customerLoginEvent->getCustomer()->getId();

        throw new \Exception('HERE!!!');
    }

    /**
     * @param CustomerLoginEvent $customerLoginEvent
     * @return mixed|null
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    protected function fetchMerchant(CustomerLoginEvent $customerLoginEvent)
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('customerId', $customerLoginEvent->getCustomer()->getId()));

        $merchant = $this->merchantRepository->search($criteria, $customerLoginEvent->getContext())->first();
        return $merchant;
    }
}
