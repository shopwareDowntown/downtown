<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;

class MerchantTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testMerchantCreateAndLogin()
    {
        $merchantData = $this->getMerchantData();

        $this->getContainer()
            ->get('merchant.repository')
            ->create([$merchantData], Context::createDefaultContext());

        $result = $this->getContainer()->get('merchant.repository')
            ->search(new Criteria([$merchantData['id']]), Context::createDefaultContext());

        self::assertSame(1, $result->count());
        self::assertNotEmpty($result->first()->getCustomerId());

        $token = $this->getContainer()
            ->get(AccountService::class)
            ->loginWithPassword(
                new DataBag([
                    'username' => $merchantData['email'],
                    'password' => $merchantData['password'],
                ]),
                $this->getContainer()->get(SalesChannelContextService::class)->get(Defaults::SALES_CHANNEL, Uuid::randomHex())
            );

        self::assertNotEmpty($token);

        $salesChannelContext = $this->getContainer()
            ->get(SalesChannelContextService::class)
            ->get(Defaults::SALES_CHANNEL, $token);

        self::assertInstanceOf(SalesChannelContextExtension::class, SalesChannelContextExtension::extract($salesChannelContext));
    }

    public function testCustomerDelete(): void {
        $merchantData = $this->getMerchantData();

        $merchantRepository = $this->getContainer()->get('merchant.repository');

        $merchantRepository
            ->create([$merchantData], Context::createDefaultContext());

        $result = $merchantRepository
            ->search(new Criteria([$merchantData['id']]), Context::createDefaultContext());

        $merchantId = $result->first()->getId();
        $customerId = $result->first()->getCustomerId();

        $customerResult = $this->getContainer()->get('customer.repository')
            ->search(new Criteria([$customerId]), Context::createDefaultContext());
        self::assertSame(1, $customerResult->count());

        $merchantRepository->delete([['id' => $merchantId]], Context::createDefaultContext());

        $result = $merchantRepository
            ->search(new Criteria([$merchantData['id']]), Context::createDefaultContext());
        self::assertSame(0, $result->count());

        $customerResult = $this->getContainer()->get('customer.repository')
            ->search(new Criteria([$customerId]), Context::createDefaultContext());
        self::assertSame(0, $customerResult->count());
    }

    private function getMerchantData(): array
    {
        $merchantId = Uuid::randomHex();

        return [
            'id' => $merchantId,
            'public' => true,
            'name' => 'FOO',
            'email' => 'BAR@BAZ.COM',
            'password' => 'ABC',

            'website' => 'http:://www.huhu.de',
            'description' => 'A comprehensible descvription',
            'phoneNumber' => '999-6666-1111',

            'salesChannelId' => Defaults::SALES_CHANNEL,
        ];
    }

}
