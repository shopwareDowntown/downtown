<?php

namespace Shopware\Production\Locali\Model;

/**
 * Class Offer
 * @package Shopware\Production\Locali\Model
 */
class Offer implements \JsonSerializable
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $subtitle = " ";

    /**
     * @var string[]
     */
    public $types = [];

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $startDate;

    /**
     * @var \DateTime|null
     */
    protected $endDate;

    /**
     * @var Organizer|null
     */
    public $organizer;

    /**
     * @var string|null
     */
    public $imageUrl;

    /**
     * @var string|null
     */
    protected $actionLink;

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var Contact
     */
    public $contact;

    /**
     * @var string[]
     */
    protected $acceptedPaymentMethods = [];

    /**
     * @var string[]
     */
    protected $tags;

    /**
     * @var Url[]
     */
    public $urls;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): Offer
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     * @return $this
     */
    public function setSubtitle(string $subtitle): Offer
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string[] $types
     * @return $this
     */
    public function setTypes(array $types): Offer
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt): Offer
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     * @return $this
     */
    public function setStartDate(?\DateTime $startDate): Offer
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     * @return $this
     */
    public function setEndDate(?\DateTime $endDate): Offer
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Organizer|null
     */
    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    /**
     * @param Organizer|null $organizer
     * @return $this
     */
    public function setOrganizer(?Organizer $organizer): Offer
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     * @return $this
     */
    public function setImageUrl(?string $imageUrl): Offer
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionLink(): ?string
    {
        return $this->actionLink;
    }

    /**
     * @param string|null $actionLink
     * @return $this
     */
    public function setActionLink(?string $actionLink): Offer
    {
        $this->actionLink = $actionLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): Offer
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setAddress(Address $address): Offer
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     * @return $this
     */
    public function setContact(Contact $contact): Offer
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAcceptedPaymentMethods(): array
    {
        return $this->acceptedPaymentMethods;
    }

    /**
     * @param string[] $acceptedPaymentMethods
     * @return $this
     */
    public function setAcceptedPaymentMethods(array $acceptedPaymentMethods): Offer
    {
        $this->acceptedPaymentMethods = $acceptedPaymentMethods;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     * @return $this
     */
    public function setTags(array $tags): Offer
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Url[]
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * @param Url[] $urls
     * @return $this
     */
    public function setUrls(array $urls): Offer
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->getTitle(),
            'subtitle' => $this->getSubtitle()
        ];
    }
}
