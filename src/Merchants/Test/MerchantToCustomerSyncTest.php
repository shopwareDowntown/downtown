<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;

class MerchantToCustomerSyncTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testMerchantCreate()
    {

        // @todo validate multiple with same email address

        $merchantData = $this->getMerchantData();

        $this->getContainer()
            ->get('merchant.repository')
            ->create([$merchantData], Context::createDefaultContext());

        $result = $this->getContainer()->get('merchant.repository')
            ->search(new Criteria([$merchantData['id']]), Context::createDefaultContext());

        self::assertSame(1, $result->count());
        self::assertNotEmpty($result->first()->getCustomerId());

//        $this->getContainer()->get(AccountService::class)
//            ->login(
//                $merchantData['email'],
//                $this->getContainer()->get(SalesChannelContextService::class)->get(Defaults::SALES_CHANNEL, Uuid::randomHex())
//            );
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
