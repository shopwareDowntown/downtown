<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Production\Merchants\Content\Merchant\Api\IndustriesController;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;

class IndustriesTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testListing(): void
    {
        $salesChannelContext = $this->getContainer()
                ->get(SalesChannelContextService::class)
                ->get(Defaults::SALES_CHANNEL, Uuid::randomHex());

        SalesChannelContextExtension::add($salesChannelContext, new MerchantEntity());

        $result = $this->getContainer()->get(IndustriesController::class)->load($salesChannelContext);

        static::assertGreaterThan(16, \count(json_decode($result->getContent(), true)));
    }
}
