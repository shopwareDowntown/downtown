<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Exception;

use Shopware\Core\Checkout\Cart\Error\Error;

class CartInvalidShippingMethod extends Error
{
    public function __construct()
    {
        $this->message = 'Cart has not allowed shipping method';

        parent::__construct($this->message);
    }

    public function getParameters(): array
    {
        return [];
    }

    public function getId(): string
    {
        return 'checkout';
    }

    public function getMessageKey(): string
    {
        return 'cartHasInvalidShippingMethod';
    }

    public function getLevel(): int
    {
        return self::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return true;
    }
}
