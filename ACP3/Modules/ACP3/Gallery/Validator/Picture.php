<?php
namespace ACP3\Modules\ACP3\Gallery\Validator;

use ACP3\Core;

/**
 * Class Picture
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Picture extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Mime
     */
    protected $mimeValidator;

    /**
     * @param Core\Lang                 $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Mime $mimeValidator
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
     * @param array $file
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($file['tmp_name'])) {
            $this->errors['file'] = $this->lang->t('gallery', 'no_picture_selected');
        }
        if (!empty($file['tmp_name']) &&
            ($this->mimeValidator->isPicture($file['tmp_name']) === false || $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $file
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $file)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!empty($file['tmp_name']) &&
            ($this->mimeValidator->isPicture($file['tmp_name']) === false || $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['file'] = $this->lang->t('gallery', 'invalid_image_selected');
        }

        $this->_checkForFailedValidation();
    }
}