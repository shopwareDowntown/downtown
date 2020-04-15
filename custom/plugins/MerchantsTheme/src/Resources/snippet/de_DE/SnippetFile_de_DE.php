<?php declare(strict_types=1);

namespace MerchantsTheme\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'merchants.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/merchants.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
    }

    public function getAuthor(): string
    {
        return 'Shopware AG';
    }

    public function isBase(): bool
    {
        return false;
    }
}
