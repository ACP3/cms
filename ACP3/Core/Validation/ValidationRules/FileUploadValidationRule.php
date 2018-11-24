<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = []): bool
    {
        $required = isset($extra['required']) ? (bool) $extra['required'] : true;

        return $this->isFileUpload($data) || ($required === false && empty($data));
    }

    /**
     * @param string|array|UploadedFile $data
     *
     * @return bool
     */
    protected function isFileUpload($data)
    {
        if ($data instanceof UploadedFile) {
            return $data->isValid() && $data->getSize() > 0;
        }

        return \is_array($data) && !empty($data['tmp_name']) && !empty($data['size']) && $data['error'] === UPLOAD_ERR_OK;
    }
}
