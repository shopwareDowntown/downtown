<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerDoubleOptInRegistrationEvent;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Content\Newsletter\Exception\SalesChannelDomainNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Production\Merchants\Content\Merchant\Exception\EmailAlreadyExistsException;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class RegistrationService
{
    /**
     * @var EntityRepositoryInterface
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

    /**
     * @var DataValidator
     */
    private $dataValidator;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $domainRepository,
        EventDispatcherInterface $eventDispatcher,
        DataValidator $dataValidator
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->systemConfigService = $systemConfigService;
        $this->domainRepository = $domainRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataValidator = $dataValidator;
    }

    public function registerMerchant(array $parameters, SalesChannelContext $salesChannelContext): string
    {
        $violations = $this->dataValidator->getViolations($parameters, $this->createValidationDefinition($salesChannelContext));
        if ($violations->count()) {
            throw new ConstraintViolationException($violations, $parameters);
        }

        $parameters['id'] = Uuid::randomHex();

        if (!$this->isMailAvailable($parameters['email'])) {
            throw new EmailAlreadyExistsException('Email address is already taken');
        }

        $this->merchantRepository->create([$parameters], $salesChannelContext->getContext());

        $criteria = new Criteria([$parameters['id']]);
        $criteria->addAssociation('customer.salutation');

        $result = $this->merchantRepository->search($criteria, $salesChannelContext->getContext());
        /** @var MerchantEntity $merchant */
        $merchant = $result->first();

        $customer = $merchant->getCustomer();
        if ($customer === null) {
            throw new CustomerNotFoundException($parameters['email']);
        }

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

    private function isMailAvailable(string $email): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $email));

        return $this->merchantRepository->searchIds($criteria, Context::createDefaultContext())->getTotal() === 0;
    }

    protected function createValidationDefinition(SalesChannelContext $salesChannelContext): DataValidationDefinition
    {
        return (new DataValidationDefinition())
            ->add('publicCompanyName', new Type('string'))
            ->add('email', new Email())
            ->add('salesChannelId', new EntityExists(['entity' => 'sales_channel', 'context' => $salesChannelContext->getContext()]))
            ->add('password', new NotBlank(), new Length(['min' => 8]));
    }
}
