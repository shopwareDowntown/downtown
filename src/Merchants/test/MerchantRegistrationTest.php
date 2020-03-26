<?php declare(strict_types=1);

namespace Shopware\Storefront\Test\Controller;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\Api\RegistrationController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $id1 = Uuid::randomHex();

        $salutationId = $this->getValidSalutationId();

        $merchantData =
            [
                'email' => Uuid::randomHex() . '@example.com',
                'password' => 'a-valid-password',
                'lastName' => 'not',
                'firstName' => 'First name',
                'salutationId' => $salutationId,
                'name' => "foo"
            ];

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), Defaults::SALES_CHANNEL, [SalesChannelContextService::CUSTOMER_ID => $id1]);

        $request = new Request();
        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT, $context);
        $this->getContainer()->get('request_stack')->push($request);

        $jsonResponse = $this->getContainer()->get(RegistrationController::class)->register(new RequestDataBag($merchantData), $context);
        $result = (array) json_decode($jsonResponse->getContent());

        self::assertNotNull($result['data']);
    }
}
