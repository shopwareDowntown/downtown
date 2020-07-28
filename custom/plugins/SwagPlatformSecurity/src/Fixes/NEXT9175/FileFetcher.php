<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9175;

use Shopware\Core\Content\Media\Exception\IllegalUrlException;
use Shopware\Core\Content\Media\File\FileUrlValidatorInterface;
use Shopware\Core\Content\Media\File\MediaFile;
use Swag\Security\Components\State;
use Symfony\Component\HttpFoundation\Request;

class FileFetcher extends \Shopware\Core\Content\Media\File\FileFetcher
{
    /**
     * @var array
     */
    private $constructorArgs;

    /**
     * @var State
     */
    private $state;

    /**
     * @var FileUrlValidatorInterface
     */
    private $fileUrlValidator;

    public function __construct(array $constructorArgs, State $state, FileUrlValidatorInterface $fileUrlValidator)
    {
        $this->constructorArgs = $constructorArgs;
        $this->state = $state;
        $this->fileUrlValidator = $fileUrlValidator;

        // @codeCoverageIgnoreStart
        if (method_exists(get_parent_class($this), '__construct')) {
            parent::__construct(... $constructorArgs);
        }
        // @codeCoverageIgnoreEnd
    }

    public function fetchFileFromURL(Request $request, string $fileName): MediaFile
    {
        if (
            $this->state->isActive('NEXT-9175') &&
            !$this->fileUrlValidator->isValid($url = $request->request->get('url'))
        ) {
            throw new IllegalUrlException($url);
        }

        return parent::fetchFileFromURL($request, $fileName);
    }
}
