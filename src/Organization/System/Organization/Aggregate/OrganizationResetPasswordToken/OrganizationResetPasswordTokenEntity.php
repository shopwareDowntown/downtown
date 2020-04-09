<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationResetPasswordToken;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;

class OrganizationResetPasswordTokenEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var OrganizationEntity
     */
    protected $organization;

    public function getToken(): string
    {
        return $this->token;
    }

    public function getOrganization(): OrganizationEntity
    {
        return $this->organization;
    }
}
