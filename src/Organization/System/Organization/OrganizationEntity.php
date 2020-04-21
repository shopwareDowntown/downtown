<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization;

use OpenApi\Annotations as OA;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Organization\System\Organization\Aggregate\OrganizationDisclaimer\OrganizationDisclaimerEntity;

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

    /**
     * @var string
     * @OA\Property()
     */
    protected $firstName;

    /**
     * @var string
     * @OA\Property()
     */
    protected $lastName;

    /**
     * @var string|null
     * @OA\Property()
     */
    protected $phone;

    /**
     * @var string|null
     * @OA\Property()
     */
    protected $postCode;

    /**
     * @var string|null
     * @OA\Property()
     */
    protected $city;

    /**
     * @OA\Property()
     * @var string
     */
    protected $imprint;

    /**
     * @OA\Property()
     * @var string
     */
    protected $tos;

    /**
     * @OA\Property()
     * @var string
     */
    protected $privacy;

    /**
     * @var string
     * @OA\Property()
     */
    protected $logoId;

    /**
     * @var MediaEntity|null
     */
    protected $logo;

    /**
     * @OA\Property()
     * @var string|null
     */
    protected $homeHeroImageId;

    /**
     * @var MediaEntity|null
     */
    protected $homeHeroImage;

    /**
     * @OA\Property()
     * @var string|null
     */
    protected $homeText;

    /**
     * @OA\Property()
     * @var OrganizationDisclaimerEntity|null
     */
    protected $disclaimer;

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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getImprint(): string
    {
        return $this->imprint;
    }

    public function getTos(): string
    {
        return $this->tos;
    }

    public function getPrivacy(): string
    {
        return $this->privacy;
    }

    public function getLogoId(): string
    {
        return $this->logoId;
    }

    public function getLogo(): ?MediaEntity
    {
        return $this->logo;
    }

    public function getHomeHeroImageId(): ?string
    {
        return $this->homeHeroImageId;
    }

    public function getHomeHeroImage(): ?MediaEntity
    {
        return $this->homeHeroImage;
    }

    public function getHomeText(): ?string
    {
        return $this->homeText;
    }

    public function getDisclaimer(): ?OrganizationDisclaimerEntity
    {
        return $this->disclaimer;
    }
}
