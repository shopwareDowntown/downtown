<?php declare(strict_types=1);

namespace Shopware\Production\Locali\LocaliOffer;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

/**
 * Class LocaliOfferEntity
 * @package Shopware\Production\Locali\LocaliOffer
 */
class LocaliOfferEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $productEntityId;

    /**
     * @var ProductEntity
     */
    protected $productEntity;

    /**
     * @var string
     */
    protected $documentId;

    /**
     * @return string
     */
    public function getProductEntityId(): string
    {
        return $this->productEntityId;
    }

    /**
     * @param string $productEntityId
     * @return $this
     */
    public function setProductEntityId(string $productEntityId): LocaliOfferEntity
    {
        $this->productEntityId = $productEntityId;

        return $this;
    }

    /**
     * @return ProductEntity
     */
    public function getProductEntity(): ProductEntity
    {
        return $this->productEntity;
    }

    /**
     * @param ProductEntity $productEntity
     * @return $this
     */
    public function setProductEntity(ProductEntity $productEntity): LocaliOfferEntity
    {
        $this->productEntity = $productEntity;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    /**
     * @param string $documentId
     * @return $this
     */
    public function setDocumentId(string $documentId): LocaliOfferEntity
    {
        $this->documentId = $documentId;

        return $this;
    }
}
