<?php declare(strict_types=1);

namespace Shopware\Production\Voucher\Exception;

use Shopware\Core\Checkout\Cart\Error\Error;

class CartContainsMultipleProductTypes extends Error
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $property;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->message = sprintf('Line item "%s" cannot be added. Only one type of product can be in the cart', $key);

        parent::__construct($this->message);
    }

    public function getParameters(): array
    {
        return ['key' => $this->key];
    }

    public function getId(): string
    {
        return $this->key;
    }

    public function getMessageKey(): string
    {
        return 'cartContainsMultipleProductTypes';
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
