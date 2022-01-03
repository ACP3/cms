<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureValidationRule extends AbstractValidationRule
{
    public function __construct(private FileUploadValidationRule $fileUploadValidationRule)
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

        if ($this->fileUploadValidationRule->isValid($data)) {
            return $this->isPicture(
                $data instanceof UploadedFile ? $data->getPathname() : $data['tmp_name'],
                $params['width'],
                $params['height'],
                $params['filesize']
            );
        }

        if ($params['required'] === false && empty($data)) {
            return true;
        }

        return false;
    }

    private function isPicture(string $file, int $width = 0, int $height = 0, int $filesize = 0): bool
    {
        $info = getimagesize($file);
        $isPicture = ($info[2] >= 1 && $info[2] <= 3);

        if ($isPicture === true) {
            $bool = true;
            // Optional parameters
            if ($this->validateOptionalParameters($file, $info, $width, $height, $filesize)) {
                $bool = false;
            }

            return $bool;
        }

        return false;
    }

    private function validateOptionalParameters(string $file, array $info, int $width, int $height, int $filesize): bool
    {
        return ($width > 0 && $info[0] > $width) ||
        ($height > 0 && $info[1] > $height) ||
        ($filesize > 0 && filesize($file) > $filesize);
    }
}
