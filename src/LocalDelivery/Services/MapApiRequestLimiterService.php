<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use PHPUnit\Util\Exception;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Production\LocalDelivery\MapApiRequestLimiter\MapApiRequestLimiterCollection;
use Shopware\Production\LocalDelivery\MapApiRequestLimiter\MapApiRequestLimiterEntity;

class MapApiRequestLimiterService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $mapApiRequestLimiterRepository;

    public function __construct(EntityRepositoryInterface $mapApiRequestLimiterRepository)
    {
        $this->mapApiRequestLimiterRepository = $mapApiRequestLimiterRepository;
    }

    public function increaseCount(string $endpointName, Context $context, int $count = 1): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('endpointName', $endpointName));
        /** @var MapApiRequestLimiterCollection $elements */
        $elements = $this->mapApiRequestLimiterRepository->search($criteria, $context)->getEntities();

        if ($elements->count() < 1) {
            throw new Exception('MapApiRequestLimiter: no limit found for endpoint: ' . $endpointName);
        }

        /** @var MapApiRequestLimiterEntity $limiter */
        $limiter = $elements->first();

        if ($limiter->getRequestCount() + $count > $limiter->getRequestLimit()) {
            return false;
        }

        $this->mapApiRequestLimiterRepository->update([
            [
                'id' => $limiter->getId(),
                'requestCount' => $limiter->getRequestCount() + $count
            ]
        ], $context);

        return true;
    }
}
