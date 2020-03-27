<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class MerchantEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var bool
     */
    protected $public;

    /**
     * @var string|null
     */
    protected $publicCompanyName;

    /**
     * @var string|null
     */
    protected $publicPhoneNumber;

    /**
     * @var string|null
     */
    protected $publicEmail;

    /**
     * @var string|null
     */
    protected $publicOpeningTimes;

    /**
     * @var string|null
     */
    protected $publicDescription;

    /**
     * @var string|null
     */
    protected $publicWebsite;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $street;

    /**
     * @var string|null
     */
    protected $zip;

    /**
     * @var string|null
     */
    protected $city;

    /**
     * @var string|null
     */
    protected $country;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $phoneNumber;

    /**
     * @var string|null
     */
    protected $customerId;

    /**
     * @var CustomerEntity|null
     */
    protected $customer;

    /**
     * @var string|null
     */
    protected $salesChannelId;

    /**
     * @var SalesChannelEntity|null
     */
    protected $salesChannel;

    /**
     * @var string|null
     */
    protected $categoryId;

    /**
     * @var CategoryEntity|null
     */
    protected $category;

    /**
     * @var ProductCollection|null
     */
    protected $products;

    /**
     * @var ShippingMethodCollection
     */
    protected $shippingMethods;

    /**
     * @var OrderCollection
     */
    protected $orders;

    /**
     * @var MediaCollection|null
     */
    protected $media;

    /**
     * @var string|null
     */
    protected $coverId;

    /**
     * @var MediaEntity|null
     */
    protected $cover;

    /**
     * @return string|null
     */
    public function getVersionId(): ?string
    {
        return $this->versionId;
    }
    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getPublicCompanyName(): ?string
    {
        return $this->publicCompanyName;
    }

    public function setPublicCompanyName(?string $publicCompanyName): void
    {
        $this->publicCompanyName = $publicCompanyName;
    }

    public function getPublicPhoneNumber(): ?string
    {
        return $this->publicPhoneNumber;
    }

    public function setPublicPhoneNumber(?string $publicPhoneNumber): void
    {
        $this->publicPhoneNumber = $publicPhoneNumber;
    }

    public function getPublicEmail(): ?string
    {
        return $this->publicEmail;
    }

    public function setPublicEmail(?string $publicEmail): void
    {
        $this->publicEmail = $publicEmail;
    }

    public function getPublicOpeningTimes(): ?string
    {
        return $this->publicOpeningTimes;
    }

    public function setPublicOpeningTimes(?string $publicOpeningTimes): void
    {
        $this->publicOpeningTimes = $publicOpeningTimes;
    }

    public function getPublicDescription(): ?string
    {
        return $this->publicDescription;
    }

    public function setPublicDescription(?string $publicDescription): void
    {
        $this->publicDescription = $publicDescription;
    }

    public function getPublicWebsite(): ?string
    {
        return $this->publicWebsite;
    }

    public function setPublicWebsite(?string $publicWebsite): void
    {
        $this->publicWebsite = $publicWebsite;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(?string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }

    public function getSalesChannel(): ?SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function setSalesChannel(?SalesChannelEntity $salesChannel): void
    {
        $this->salesChannel = $salesChannel;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }

    public function setProducts(?ProductCollection $products): void
    {
        $this->products = $products;
    }

    public function getShippingMethods(): ShippingMethodCollection
    {
        return $this->shippingMethods;
    }

    public function setShippingMethods(ShippingMethodCollection $shippingMethods): void
    {
        $this->shippingMethods = $shippingMethods;
    }

    public function getOrders(): OrderCollection
    {
        return $this->orders;
    }

    public function setOrders(OrderCollection $orders): void
    {
        $this->orders = $orders;
    }

    public function getMedia(): ?MediaCollection
    {
        return $this->media;
    }

    public function setMedia(?MediaCollection $media): void
    {
        $this->media = $media;
    }

    /**
     * @return MediaEntity|null
     */
    public function getCover(): ?MediaEntity
    {
        return $this->cover;
    }

    /**
     * @param MediaEntity|null $cover
     */
    public function setCover(?MediaEntity $cover): void
    {
        $this->cover = $cover;
    }

    public function getCoverId(): ?string
    {
        return $this->coverId;
    }

    public function setCoverId(?string $coverId): void
    {
        $this->coverId = $coverId;
    }
}
