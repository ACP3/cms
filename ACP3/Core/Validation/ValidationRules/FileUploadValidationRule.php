<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class FileUploadValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class FileUploadValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $required = isset($extra['required']) ? (bool)$extra['required'] : true;

        return ($this->isFileUpload($data) || ($required === false && empty($data)));
    }

    /**
     * @param string|array $data
     *
     * @return bool
     */
    protected function isFileUpload($data)
    {
        return (is_array($data) && !empty($data['tmp_name']) && !empty($data['size']) && $data['error'] === UPLOAD_ERR_OK);
    }

}