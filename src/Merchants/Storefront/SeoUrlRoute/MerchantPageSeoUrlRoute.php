<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\SeoUrlRoute;

use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlExtractIdResult;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

class MerchantPageSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'storefront.merchant.detail';
    public const DEFAULT_TEMPLATE = '{{ merchant.publicCompanyName }}';

    /**
     * @var MerchantDefinition
     */
    private $merchantDefinition;

    public function __construct(MerchantDefinition $merchantDefinition)
    {
        $this->merchantDefinition = $merchantDefinition;
    }

    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->merchantDefinition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE
        );
    }

    public function prepareCriteria(Criteria $criteria): void
    {
    }

    public function getMapping(Entity $entity, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        if (!$entity instanceof MerchantEntity) {
            throw new \InvalidArgumentException('Expected MerchantEntity');
        }

        return new SeoUrlMapping(
            $entity,
            ['id' => $entity->getId()],
            [
                'merchant' => $entity->jsonSerialize(),
            ]
        );
    }

    public function getSeoVariables(): array
    {
        return [];
    }
}
