<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        $required = !isset($extra['required']) || $extra['required'];

        return ($required === false && empty($data)) || $this->isFileUpload($data);
    }

    /**
     * @param mixed[]|string|UploadedFile $data
     */
    protected function isFileUpload(array|string|UploadedFile $data): bool
    {
        if ($data instanceof UploadedFile) {
            return $data->isValid() && $data->getSize() > 0;
        }

        return \is_array($data) && !empty($data['tmp_name']) && !empty($data['size']) && $data['error'] === UPLOAD_ERR_OK;
    }
}
