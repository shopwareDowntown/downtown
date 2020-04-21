<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\AbstractRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Production\Organization\Exception\OrganizationNotLoggedInException;
use Shopware\Production\Organization\System\Organization\Aggregate\OrganizationAccessToken\OrganizationAccessTokenEntity;
use Symfony\Component\HttpFoundation\Request;

class OrganizationApiRouteScope extends AbstractRouteScope
{
    public const ID = 'organization-api';
    public const ORGANIZATION_OBJECT = 'portal-organization-object';

    /**
     * @var EntityRepositoryInterface
     */
    private $organizationAccessToken;

    /**
     * @var SalesChannelContextFactory
     */
    private $salesChannelContextFactory;

    public function __construct(EntityRepositoryInterface $organizationAccessToken, SalesChannelContextFactory $salesChannelContextFactory)
    {
        $this->organizationAccessToken = $organizationAccessToken;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    public function isAllowed(Request $request): bool
    {
        if ($request->attributes->get('auth_required', true)) {
            $this->validateLogin($request);
        }

        return strpos($request->getPathInfo(), '/organization-api') === 0;
    }

    public function getId(): string
    {
        return self::ID;
    }

    private function validateLogin(Request $request): void
    {
        $headerToken = $request->headers->get(PlatformRequest::HEADER_CONTEXT_TOKEN);

        if (!$headerToken) {
            throw new OrganizationNotLoggedInException('Organization not logged in');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('token', $headerToken));
        $criteria->addAssociation('organization');

        /** @var OrganizationAccessTokenEntity|null $token */
        $token = $this->organizationAccessToken->search($criteria, Context::createDefaultContext())->first();

        if (!$token) {
            throw new OrganizationNotLoggedInException('Organization not logged in');
        }

        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID, $token->getOrganization()->getSalesChannelId());
        $request->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT, $this->salesChannelContextFactory->create($headerToken, $token->getOrganization()->getSalesChannelId()));
        $request->attributes->set(self::ORGANIZATION_OBJECT, $token->getOrganization());
    }
}
