<?php declare(strict_types=1);

namespace Shopware\Merchants\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\Api\MerchantProductController;
use Symfony\Component\HttpFoundation\Request;

class MerchantProductTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testCreateCustomProductField(): void
    {
        [
            $token,
            $salesChannelContext
        ] = $this->login();

        $productController = $this->getContainer()->get(MerchantProductController::class);
        $request = new Request(
            [],
            [
                'name' => 'Name',
                'description' => 'description',
                'stock' => '1',
                'price' => '1',
                'productType' => 'product',
                'tax' => '19.00',
            ]
        );

        $jsonResponse = $productController->create($request, $salesChannelContext);

        $result = json_decode($jsonResponse->getContent(), true);

        self::assertEquals('product', $result['data']['customFields']['productType']);
    }

    public function testGetList(): void
    {
        $this->markTestSkipped('Not working');
        [
            $token,
            $salesChannelContext
        ] = $this->login();

        $productController = $this->getContainer()->get(MerchantProductController::class);
        $request = new Request(
            [],
            [
                'name' => 'Name',
                'description' => 'description',
                'stock' => '1',
                'price' => '1',
                'productType' => 'product',
                'tax' => '19.00',
            ]
        );

        $productController->create($request, $salesChannelContext);

        $jsonResponse = $productController->getList($salesChannelContext);

        $result = json_decode($jsonResponse->getContent(), true);

        self::assertEquals('product', $result['data'][0]['productType']);
    }

    private function login(): array
    {
        $merchantData = $this->getMinimalMerchantData();

        $this->getContainer()
            ->get('merchant.repository')
            ->create([$merchantData], Context::createDefaultContext());

        $result = $this->getContainer()->get('merchant.repository')
            ->search(new Criteria([$merchantData['id']]), Context::createDefaultContext());

        $this->getContainer()->get('customer.repository')->update([[
            'id' => $result->first()->getCustomerId(),
            'active' => true,
        ]], Context::createDefaultContext());

        $token = $this->getContainer()
            ->get(AccountService::class)
            ->loginWithPassword(
                new DataBag([
                    'username' => $merchantData['email'],
                    'password' => $merchantData['password'],
                ]),
                $this->getContainer()->get(SalesChannelContextService::class)->get(Defaults::SALES_CHANNEL, Uuid::randomHex())
            );

        $salesChannelContext = $this->getContainer()
            ->get(SalesChannelContextService::class)
            ->get(Defaults::SALES_CHANNEL, $token);

        return [$token, $salesChannelContext];
    }

    private function getMinimalMerchantData(): array
    {
        $merchantId = Uuid::randomHex();

        return [
            'id' => $merchantId,

            'publicCompanyName' => 'FOO',
            'email' => 'BAR@BAZ.COM',
            'password' => 'ABC',

            'salesChannelId' => Defaults::SALES_CHANNEL,
        ];
    }
}
