<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\SalesChannelContextExtension;

class MerchantSalesChannelContextService implements SalesChannelContextServiceInterface
{
    public const PERSISTSER_KEY = 'merchant_id';

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;
    /**
     * @var SalesChannelContextPersister
     */
    private $contextPersister;

    /**
     * @var SalesChannelContextServiceInterface
     */
    private $decorated;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        SalesChannelContextPersister $contextPersister,
        SalesChannelContextServiceInterface $decorated
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->contextPersister = $contextPersister;
        $this->decorated = $decorated;
    }

    public function get(string $salesChannelId, string $token, ?string $languageId = null): SalesChannelContext
    {
        $salesChannelContext = $this->decorated->get($salesChannelId, $token, $languageId);
        $params = $this->contextPersister->load($token);

        if (!\array_key_exists(self::PERSISTSER_KEY, $params)) {
            return $salesChannelContext;
        }

        $merchant = $this->merchantRepository
            ->search(new Criteria([$params[self::PERSISTSER_KEY]]), $salesChannelContext->getContext())
            ->first();

        if (!$merchant) {
            return $salesChannelContext;
        }

        SalesChannelContextExtension::add($salesChannelContext, $merchant);

        return $salesChannelContext;
    }
}
