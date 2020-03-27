<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\CascadeDeleteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomerSync implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        EntityRepositoryInterface $customerRepository,
        EntityRepositoryInterface $merchantRepository,
        Connection $connection
    ) {
        $this->customerRepository = $customerRepository;
        $this->merchantRepository = $merchantRepository;
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'merchant.written' => 'syncToCustomer',
            PreWriteValidationEvent::class => 'deleteCascadeCustomers'
        ];
    }

    public function deleteCascadeCustomers(PreWriteValidationEvent $event)
    {
        $context = $event->getContext();

        foreach ($event->getCommands() as $command) {
            if (!($command instanceof CascadeDeleteCommand) || !($command->getDefinition() instanceof CustomerDefinition)) {
                continue;
            }

            $entityExistence = $command->getEntityExistence();

            if (!$entityExistence->exists()) {
                continue;
            }

            $this->customerRepository->delete([['id' => Uuid::fromBytesToHex($command->getPrimaryKey()['id'])]], $context);
        }
    }

    public function syncToCustomer(EntityWrittenEvent $event): void
    {
        if (count($event->getErrors())) {
            return;
        }

        $saneDefaults = [
            'active' => false,
            'doubleOptInRegistration' => true,
            'doubleOptInEmailSentDate' => new \DateTimeImmutable(),
            'hash' => Uuid::randomHex(),
            'groupId' => Defaults::FALLBACK_CUSTOMER_GROUP,
            'defaultPaymentMethodId' => $this->fetchRandomPaymentMethodId(),
            'languageId' => Defaults::LANGUAGE_SYSTEM,
            'defaultBillingAddress' => [
                'countryId' => $this->fetchRandomCountry(),
                'salutationId' => $this->fetchUnspecifiedSalutation(),
                'firstName' => '&nbsp;',
                'lastName' => '&nbsp;',
                'zipcode' => '123465',
                'city' => 'ABC',
                'street' => 'Sesamestreet'
            ],
            'defaultShippingAddress' => [
                'countryId' => $this->fetchRandomCountry(),
                'salutationId' => $this->fetchUnspecifiedSalutation(),
                'firstName' => '&nbsp;',
                'lastName' => '&nbsp;',
                'zipcode' => '123465',
                'city' => 'ABC',
                'street' => 'Sesamestreet'
            ],
            'salutationId' => $this->fetchUnspecifiedSalutation(),
        ];

        $merchantUpdate = [];

        $merchants = $this->merchantRepository->search(new Criteria($event->getIds()), $event->getContext());

        $customers = [];
        foreach ($event->getWriteResults() as $writeResult) {
            $customer = [$this->extractValuesIfPresent($writeResult->getPayload(), 'firstName', 'lastName', 'email', 'password')];

            if ($customer === [[]]) {
                continue;
            }

            if (!$writeResult->getExistence()->exists()) {
                $newCustomerId = Uuid::randomHex();

                $customer[] = $saneDefaults;
                $customer[] = [
                    'id' => $newCustomerId,
                    'customerNumber' => Uuid::randomHex(),
                    'salesChannelId' => Defaults::SALES_CHANNEL,

                    // only possible because they are not part of the registration
                    'firstName' => $writeResult->getPayload()['publicCompanyName'],
                    'lastName' => $writeResult->getPayload()['publicCompanyName'],
                ];

                $merchantUpdate[] = [
                    'id' => $writeResult->getPrimaryKey(),
                    'customerId' => $newCustomerId,
                ];
            } else {
                $customer[] = ['id' => $merchants->get($writeResult->getPrimaryKey())->getCustomerId()];
            }

            //todo where is the customerId if it is not a insert???

            $customers[] = array_merge(...$customer);
        }

        if ($customers === []) {
            return;
        }

        $this->customerRepository->upsert($customers, $event->getContext());

        if($merchantUpdate === []) {
            return;
        }

        $this->merchantRepository->update($merchantUpdate, $event->getContext());
    }

    private function extractValuesIfPresent(array $data, string ...$keys): array
    {
        $filteredData = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $filteredData[$key] = $data[$key];
            }
        }

        return $filteredData;
    }

    private function fetchRandomPaymentMethodId(): string
    {
        return (string)$this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM payment_method WHERE active=1 LIMIT 1');
    }

    private function fetchUnspecifiedSalutation(): string
    {
        return (string)$this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM salutation WHERE salutation_key="not_specified" LIMIT 1');
    }

    private function fetchRandomCountry(): string
    {
        return (string)$this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM country LIMIT 1');
    }
}
