<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\MapApiRequestLimiter;


use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MapApiRequestLimiterEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $endpointName;

    /**
     * @var int
     */
    protected $requestCount;

    /**
     * @var int
     */
    protected $requestLimit;

    /**
     * @return string
     */
    public function getEndpointName(): string
    {
        return $this->endpointName;
    }

    /**
     * @param string $endpointName
     */
    public function setEndpointName(string $endpointName): void
    {
        $this->endpointName = $endpointName;
    }

    /**
     * @return int
     */
    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    /**
     * @param int $requestCount
     */
    public function setRequestCount(int $requestCount): void
    {
        $this->requestCount = $requestCount;
    }

    /**
     * @return int
     */
    public function getRequestLimit(): int
    {
        return $this->requestLimit;
    }

    /**
     * @param int $requestLimit
     */
    public function setRequestLimit(int $requestLimit): void
    {
        $this->requestLimit = $requestLimit;
    }
}
