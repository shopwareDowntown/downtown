<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\DeliveryRoute;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyEntity;

class DeliveryRouteEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var DeliveryBoyEntity
     */
    protected $deliveryBoy;

    /**
     * @var array
     */
    protected $routeWaypoints;

    /**
     * @return DeliveryBoyEntity
     */
    public function getDeliveryBoy(): DeliveryBoyEntity
    {
        return $this->deliveryBoy;
    }

    /**
     * @param DeliveryBoyEntity $deliveryBoy
     */
    public function setDeliveryBoy(DeliveryBoyEntity $deliveryBoy): void
    {
        $this->deliveryBoy = $deliveryBoy;
    }

    /**
     * @return array
     */
    public function getRouteWaypoints(): array
    {
        return $this->routeWaypoints;
    }

    /**
     * @param array $routeWaypoints
     */
    public function setRouteWaypoints(array $routeWaypoints): void
    {
        $this->routeWaypoints = $routeWaypoints;
    }


}
