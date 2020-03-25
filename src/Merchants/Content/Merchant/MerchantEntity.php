<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class MerchantEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var null|string
     */
    public $customerId;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var null|string
     */
    public $website;

    /**
     * @var null|string
     */
    public $phoneNumber;

    /**
     * @var null|CustomerCollection
     */
    protected $customers;
}
