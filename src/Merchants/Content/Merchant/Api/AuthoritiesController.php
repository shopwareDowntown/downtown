<?php

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
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
     * @Route(name="merchant-api.authorities.load", path="/merchant-api/authorities")
     */
    public function load(): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));

        $items = $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->getElements();

        $result = [];

        /** @var SalesChannelEntity $item */
        foreach ($items as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'domain' => $item->getDomains()->first()->getUrl()
            ];
        }

        return new JsonResponse($result);
    }
}
