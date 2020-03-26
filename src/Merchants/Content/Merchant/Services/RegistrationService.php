<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountRegistrationService as CustomerRegistrationService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class RegistrationService
{
    /**
     * @var CustomerRegistrationService
     */
    private $customerRegistrationService;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(
        CustomerRegistrationService $customerRegistrationService,
        Connection $connection,
        EntityRepository $entityRepository
    )
    {
        $this->customerRegistrationService = $customerRegistrationService;
        $this->connection = $connection;
        $this->entityRepository = $entityRepository;
    }

    public function registerMerchant(RequestDataBag $requestDataBag, SalesChannelContext $salesChannelContext): string {
        $requestDataWithDefaultValues = $this->createDefaultDataForMerchant($requestDataBag);

        $parameters = $requestDataBag->all();
        $this->validateMerchantData($parameters);

        $customerId = $this->customerRegistrationService->register($requestDataWithDefaultValues, false, $salesChannelContext);

        $parameters['id'] = Uuid::randomHex();
        $parameters['customerId'] = $customerId;
        $parameters['salesChannelId'] = $salesChannelContext->getSalesChannel()->getId();

        $this->entityRepository->create([$parameters], $salesChannelContext->getContext());

        return $parameters['id'];
    }

    private function validateMerchantData(array $merchantData): void {

    }

    private function createDefaultDataForMerchant(RequestDataBag $requestDataBag): DataBag {
        $data = $requestDataBag->all();

        return new DataBag(array_merge($data, $this->getDefaultData()));
    }

    private function getDefaultData(): array {
        return [
            'groupId' => Defaults::FALLBACK_CUSTOMER_GROUP,
            'defaultPaymentMethodId' => $this->fetchRandomPaymentMethodId(),
            'languageId' => Defaults::LANGUAGE_SYSTEM,
            'billingAddress' => [
                'countryId' => $this->fetchRandomCountry(),
                'salutationId' => $this->fetchUnspecifiedSalutation(),
                'firstName' => '&nbsp;',
                'lastName' => '&nbsp;',
                'zipcode' => '123465',
                'city' => 'ABC',
                'street' => 'Sesamestreet'
            ],
            'shippingAddress' => [
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
    }

    private function fetchMerchantIdForCustomerId(string $customerId): string {
       return (string) $this->connection
            ->fetchColumn("SELECT LOWER(HEX(id)) FROM merchant WHERE customer_id = \"{$customerId}\"");
    }

    private function fetchRandomPaymentMethodId(): string
    {
        return (string) $this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM payment_method WHERE active=1 LIMIT 1');
    }

    private function fetchUnspecifiedSalutation(): string
    {
        return (string) $this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM salutation WHERE salutation_key="not_specified" LIMIT 1');
    }

    private function fetchRandomCountry(): string
    {
        return (string) $this->connection
            ->fetchColumn('SELECT LOWER(HEX(id)) FROM country LIMIT 1');
    }
}
