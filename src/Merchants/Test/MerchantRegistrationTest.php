<?php declare(strict_types=1);

namespace Shopware\Merchants\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\Api\RegistrationController;
use Shopware\Storefront\Test\Controller\StorefrontControllerTestBehaviour;

class MerchantRegistrationTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StorefrontControllerTestBehaviour;

    public function testMerchantRegistration(): void
    {
        $merchantData =
            [
                'email' => Uuid::randomHex() . '@example.com',
                'password' => 'a-valid-password',
                'publicCompanyName' => 'foo',
                'salesChannelId' => Defaults::SALES_CHANNEL
            ];

        $context = $this->getContainer()->get(SalesChannelContextService::class)
            ->get(Defaults::SALES_CHANNEL, Uuid::randomHex());

        $jsonResponse = $this->getContainer()->get(RegistrationController::class)->register(new RequestDataBag($merchantData), $context);
        $result = json_decode($jsonResponse->getContent(), true);

        self::assertTrue($result['success']);
    }
}
