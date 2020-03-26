<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MerchantLogin implements EventSubscriberInterface
{

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;
    /**
     * @var SalesChannelContextPersister
     */
    private $contextPersister;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        SalesChannelContextPersister $contextPersister
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->contextPersister = $contextPersister;
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

        SalesChannelContextExtension::add(
            $customerLoginEvent->getSalesChannelContext(),
            $merchant
        );

        $this->contextPersister->save(
            $customerLoginEvent->getContextToken(),
            [self::PERSISTSER_KEY => $merchant->getId()]
        );
    }

    protected function fetchMerchant(CustomerLoginEvent $customerLoginEvent): ?MerchantEntity
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('customerId', $customerLoginEvent->getCustomer()->getId()));

        return $this->merchantRepository
            ->search($criteria, $customerLoginEvent->getContext())->first();
    }
}
