<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\AbstractRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Production\Merchants\Content\Merchant\Aggregate\MerchantAccessToken\MerchantAccessTokenEntity;
use Shopware\Production\Merchants\Exception\MerchantNotLoggedinException;
use Symfony\Component\HttpFoundation\Request;

class MerchantApiRouteScope extends AbstractRouteScope
{
    public const ID = 'merchant-api';
    public const MERCHANT_OBJECT = 'portal-merchant-object';

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantAccessTokenRepository;

    /**
     * @var SalesChannelContextFactory
     */
    private $salesChannelContextFactory;

    public function __construct(EntityRepositoryInterface $merchantAccessTokenRepository, SalesChannelContextFactory $salesChannelContextFactory)
    {
        $this->merchantAccessTokenRepository = $merchantAccessTokenRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    public function isAllowed(Request $request): bool
    {
        if ($request->attributes->get('auth_required', true)) {
            $this->validateLogin($request);
        }

        return strpos($request->getPathInfo(), '/merchant-api') === 0;
    }

    public function getId(): string
    {
        return self::ID;
    }

    private function validateLogin(Request $request): void
    {
        $headerToken = $request->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN);

        if (!$headerToken) {
            throw new MerchantNotLoggedinException('Merchant not logged in');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('token', $headerToken));
        $criteria->addAssociation('merchant');

        /** @var MerchantAccessTokenEntity|null $token */
        $token = $this->merchantAccessTokenRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$token) {
            throw new MerchantNotLoggedinException('Merchant not logged in');
        }

        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID, $token->getMerchant()->getSalesChannelId());
        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT, $this->salesChannelContextFactory->create($headerToken, $token->getMerchant()->getSalesChannelId()));
        $request->attributes->set(self::MERCHANT_OBJECT, $token->getMerchant());
    }
}
