<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyEntity;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeliveryBoySession
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function login(DeliveryBoyEntity $deliveryBoyEntity): void
    {
        $this->session->set('deliveryBoyId', $deliveryBoyEntity->getId());
        $this->session->set('deliveryBoyEmail', $deliveryBoyEntity->getEmail());
        $this->session->set('deliveryBoyPassword', $deliveryBoyEntity->getPassword());
    }

    public function getDeliveryBoyData(): array
    {
        return [
            'id' => $this->session->get('deliveryBoyId'),
            'email' => $this->session->get('deliveryBoyEmail'),
            'password' => $this->session->get('deliveryBoyPassword'),
        ];
    }

    public function logout(): void
    {
        $this->session->remove('deliveryBoyId');
        $this->session->remove('deliveryBoyEmail');
        $this->session->remove('deliveryBoyPassword');
    }

    public function getSessionId(): string
    {
        return $this->session->getId();
    }
}
