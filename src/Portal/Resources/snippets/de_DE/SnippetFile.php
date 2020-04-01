<?php declare(strict_types=1);

namespace Shopware\Production\Portal\Resources\snippets\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'Portal';
    }

    public function getPath(): string
    {
        return __DIR__ . '/translation.json';
    }

    /**
     * @inheritDoc
     */
    public function getIso(): string
    {
        return 'de-DE';
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): string
    {
        return 'shopware-AG';
    }

    /**
     * @inheritDoc
     */
    public function isBase(): bool
    {
        return false;
    }
}
