<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class AuthoritiesController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    public function __construct(EntityRepositoryInterface $salesChannelRepository)
    {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    /**
     * @deprecated Use route /organization-api/v{version}/organizations instead
     * @Route(name="merchant-api.authorities.load", path="/merchant-api/v{version}/authorities", defaults={"auth_required"=false})
     */
    public function load(): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        /** @var SalesChannelCollection $salesChannelCollection */
        $salesChannelCollection = $this->salesChannelRepository->search($criteria, Context::createDefaultContext());

        $result = [];
        foreach ($salesChannelCollection as $salesChannel) {
            $domainCollection = $salesChannel->getDomains();
            if ($domainCollection === null) {
                continue;
            }

            $domainEntity = $domainCollection->first();
            if ($domainEntity === null) {
                continue;
            }

            $result[] = [
                'id' => $salesChannel->getId(),
                'name' => $salesChannel->getName(),
                'domain' => $domainEntity->getUrl(),
                'accessKey' => $salesChannel->getAccessKey(),
            ];
        }

        return new JsonResponse($result);
    }
}
