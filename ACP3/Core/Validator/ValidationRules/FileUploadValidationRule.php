<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class FileUploadValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class FileUploadValidationRule extends AbstractValidationRule
{
    const NAME = 'file_upload';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $required = isset($extra['required']) ? (bool)$extra['required'] : true;

        return ($this->isFileUpload($data) || ($required === false && empty($data)));
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function isFileUpload($data)
    {
        return (is_array($data) && !empty($data['tmp_name']) && !empty($data['size']) && $data['error'] === UPLOAD_ERR_OK);
    }

}