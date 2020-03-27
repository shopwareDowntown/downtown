<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerDoubleOptInRegistrationEvent;
use Shopware\Core\Content\Newsletter\Exception\SalesChannelDomainNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RegistrationService
{
    /**
     * @var EntityRepository
     */
    private $merchantRepository;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private $domainRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EntityRepository $merchantRepository,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $domainRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->systemConfigService = $systemConfigService;
        $this->domainRepository = $domainRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function registerMerchant(array $parameters, SalesChannelContext $salesChannelContext): string
    {
        $parameters['id'] = Uuid::randomHex();

        $this->merchantRepository->create([$parameters], $salesChannelContext->getContext());

        $criteria = new Criteria([$parameters['id']]);
        $criteria->addAssociation('customer.salutation');

        $result = $this->merchantRepository->search($criteria, $salesChannelContext->getContext());
        /** @var MerchantEntity $merchant */
        $merchant = $result->first();

        $customer = $merchant->getCustomer();

        try {
            $this->createDoubleOptInEvent($salesChannelContext, $customer);
        } catch (SalesChannelDomainNotFoundException $e) {
            //nth
        }

        return $parameters['id'];
    }

    private function getConfirmUrl(SalesChannelContext $context, CustomerEntity $customer): string
    {
        $domainUrl = $this->systemConfigService
            ->get('core.loginRegistration.doubleOptInDomain', $context->getSalesChannel()->getId());

        if (!$domainUrl) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('salesChannelId', $context->getSalesChannel()->getId()));
            $criteria->setLimit(1);

            $domain = $this->domainRepository
                ->search($criteria, $context->getContext())
                ->first();

            if (!$domain) {
                throw new SalesChannelDomainNotFoundException($context->getSalesChannel());
            }

            $domainUrl = $domain->getUrl();
        }

        return sprintf(
            $domainUrl . '/merchant/registration/confirm?em=%s&hash=%s',
            hash('sha1', $customer->getEmail()),
            $customer->getHash()
        );
    }

    /**
     * @param SalesChannelContext $salesChannelContext
     * @param CustomerEntity|null $customer
     * @throws SalesChannelDomainNotFoundException
     */
    private function createDoubleOptInEvent(SalesChannelContext $salesChannelContext, CustomerEntity $customer): void
    {
        $url = $this->getConfirmUrl($salesChannelContext, $customer);
        $event = new CustomerDoubleOptInRegistrationEvent($customer, $salesChannelContext, $url);

        $this->eventDispatcher->dispatch($event);
    }
}
