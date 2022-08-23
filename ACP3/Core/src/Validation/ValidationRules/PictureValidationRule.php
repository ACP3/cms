<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly FileUploadValidationRule $fileUploadValidationRule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        $params = array_merge([
            'width' => 0,
            'height' => 0,
            'filesize' => 0,
            'required' => true,
        ], $extra);

        if ($params['required'] === false && empty($data)) {
            return true;
        }

        if ($this->fileUploadValidationRule->isValid($data)) {
            return $this->isPicture(
                $data instanceof UploadedFile ? $data->getPathname() : $data['tmp_name'],
                $params['width'],
                $params['height'],
                $params['filesize']
            );
        }

        return false;
    }

    private function isPicture(string $file, int $width, int $height, int $filesize): bool
    {
        $info = getimagesize($file);

        if (\in_array($info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return $this->isInDimensionAndSizeConstraints($file, $info, $width, $height, $filesize);
        }

        return false;
    }

    /**
     * @param mixed[] $info
     */
    private function isInDimensionAndSizeConstraints(string $file, array $info, int $maxWidth, int $maxHeight, int $maxFilesize): bool
    {
        if ($maxWidth > 0 && $info[0] > $maxWidth) {
            return false;
        }
        if ($maxHeight > 0 && $info[1] > $maxHeight) {
            return false;
        }
        if ($maxFilesize > 0 && filesize($file) > $maxFilesize) {
            return false;
        }

        return true;
    }
}
