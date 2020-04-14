<?php declare(strict_types=1);

namespace Shopware\Production\Portal\Hacks;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Theme\ThemeEntity;

/**
 * This decoration fixes http cache issues while switching theme settings
 * @see https://issues.shopware.com/issues/NEXT-8059
 */
class ObjectCacheKeyFinder extends \Shopware\Storefront\Framework\Cache\ObjectCacheKeyFinder
{
    /**
     * @var ObjectCacheKeyFinder
     */
    private $core;

    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    public function __construct(\Shopware\Storefront\Framework\Cache\ObjectCacheKeyFinder $core, EntityRepositoryInterface $themeRepository)
    {
        $this->core = $core;
        $this->salesChannelRepository = $themeRepository;
    }

    public function find(array $data, SalesChannelContext $context): array
    {
        $tags = $this->core->find($data, $context);
        $theme = $this->fetchTheme($context->getSalesChannel()->getId());

        if ($theme) {
            $tags[] = 'theme-' . $theme->getId();
        }

        $tags[] = 'sales_channel-' . $context->getSalesChannel()->getId();

        return $tags;
    }

    private function fetchTheme(string $id): ?ThemeEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salesChannels.id', $id));
        return $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->first();
    }
}
