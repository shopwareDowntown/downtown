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
    protected $mediaId;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
