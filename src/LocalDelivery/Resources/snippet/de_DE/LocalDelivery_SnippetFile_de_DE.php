<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class LocalDelivery_SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'storefront.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/storefront.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
    }

    public function getAuthor(): string
    {
        return 'Shopware';
    }

    public function isBase(): bool
    {
        return false;
    }
}
