<?php


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
    protected $count;

    /**
     * @var int
     */
    protected $limit;

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
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
