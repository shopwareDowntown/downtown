<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Exception;

use Shopware\Core\Checkout\Cart\Error\Error;

class CartContainsStoreWindowLineItem extends Error
{
    private const MESSAGE_KEY = 'cartWrongProductType';

    /**
     * @var string
     */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->message = sprintf('Line item "%s" cannot be added. It has a wrong product type.', $key);

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
        return self::MESSAGE_KEY;
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
