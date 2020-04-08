<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

/**
 * @OA\Schema()
 */
class OrganizationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     * @OA\Property()
     */
    protected $email;

    /**
     * @var string
     * @OA\Property()
     */
    protected $password;

    /**
     * @var string
     * @OA\Property()
     */
    protected $salesChannelId;

    /**
     * @var SalesChannelEntity|null
     */
    protected $salesChannel;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }
}
