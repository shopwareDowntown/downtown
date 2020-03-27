<?php declare(strict_types=1);

namespace Shopware\Storefront\Test\Controller;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\Api\RegistrationController;

class MerchantRegistrationTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StorefrontControllerTestBehaviour;

    /**
     * @var EntityRepositoryInterface
     */
    private $customerRepository;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testMerchantRegistration(): void
    {
        $merchantData =
            [
                'email' => Uuid::randomHex() . '@example.com',
                'password' => 'a-valid-password',
                'publicCompanyName' => "foo",
                'salesChannelId' => Defaults::SALES_CHANNEL
            ];

        $context = $this->getContainer()->get(SalesChannelContextService::class)
            ->get(Defaults::SALES_CHANNEL, Uuid::randomHex());

        $jsonResponse = $this->getContainer()->get(RegistrationController::class)->register(new RequestDataBag($merchantData), $context);
        $result = (array) json_decode($jsonResponse->getContent());

        self::assertTrue($result['success']);
    }
}
