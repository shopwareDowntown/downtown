<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Aggregate\OrganizationDisclaimer;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class OrganizationDisclaimerEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string|null
     */
    protected $text;

    /**
     * @var string|null
     */
    protected $imageId;

    /**
     * @var MediaEntity|null
     */
    protected $image;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getImageId(): ?string
    {
        return $this->imageId;
    }

    public function getImage(): ?MediaEntity
    {
        return $this->image;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
