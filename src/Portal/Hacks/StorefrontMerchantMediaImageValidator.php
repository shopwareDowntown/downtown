<?php declare(strict_types=1);

namespace Shopware\Production\Portal\Hacks;

use Shopware\Storefront\Framework\Media\Exception\FileTypeNotAllowedException;
use Shopware\Storefront\Framework\Media\StorefrontMediaValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorefrontMerchantMediaImageValidator implements StorefrontMediaValidatorInterface
{
    public function getType(): string
    {
        return 'merchant_images';
    }

    public function validate(UploadedFile $file): void
    {
        $valid = $this->checkMimeType($file, [
            'jpe|jpg|jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
        ]);

        if (!$valid) {
            throw new FileTypeNotAllowedException($file->getMimeType(), $this->getType());
        }

        // additional mime type validation
        // we detect the mime type over the `getimagesize` extension
        $imageSize = getimagesize($file->getPath() . '/' . $file->getFileName());
        if ($imageSize['mime'] !== $file->getMimeType()) {
            throw new FileTypeNotAllowedException($file->getMimeType(), $this->getType());
        }
    }

    protected function checkMimeType(UploadedFile $file, array $allowedMimeTypes): bool
    {
        foreach ($allowedMimeTypes as $fileEndings => $mime) {
            $fileEndings = explode('|', $fileEndings);

            if (!in_array($file->getClientOriginalExtension(), $fileEndings, true)) {
                continue;
            }

            if (is_array($mime) && in_array($file->getMimeType(), $mime, true)) {
                return true;
            }
        }

        return false;
    }
}
