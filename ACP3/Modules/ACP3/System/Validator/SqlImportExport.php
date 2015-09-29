<?php
namespace ACP3\Modules\ACP3\System\Validator;

use ACP3\Core;

/**
 * Class SqlImportExport
 * @package ACP3\Modules\ACP3\System\Validator
 */
class SqlImportExport extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Mime
     */
    protected $mimeValidator;

    /**
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     * @param \ACP3\Core\Validator\Rules\Mime $mimeValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Mime $mimeValidator
    )
    {
        parent::__construct($lang, $validate);

        $this->mimeValidator = $mimeValidator;
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSqlExport(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['tables']) || is_array($formData['tables']) === false) {
            $this->errors['tables'] = $this->lang->t('system', 'select_sql_tables');
        }
        if ($formData['output'] !== 'file' && $formData['output'] !== 'text') {
            $this->errors['output'] = $this->lang->t('system', 'select_output');
        }
        if (in_array($formData['export_type'], ['complete', 'structure', 'data']) === false) {
            $this->errors['export-type'] = $this->lang->t('system', 'select_export_type');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param array $file
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSqlImport(array $formData, array $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['text']) && empty($file['size'])) {
            $this->errors['upload'] = $this->lang->t('system', 'type_in_text_or_select_sql_file');
        }
        if (!empty($file['size']) &&
            (!$this->mimeValidator->mimeType($file['tmp_name'], 'text/plain') ||
                $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['upload-file'] = $this->lang->t('system', 'select_sql_file');
        }

        $this->_checkForFailedValidation();
    }
}