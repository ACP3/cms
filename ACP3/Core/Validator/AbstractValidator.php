<?php
namespace ACP3\Core\Validator;

use ACP3\Core;

/**
 * Class AbstractValidator
 * @package ACP3\Core\Validator
 */
class AbstractValidator
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param Core\Lang $lang
     * @param Rules\Misc $validate
     */
    public function __construct(
        Core\Lang $lang,
        Rules\Misc $validate
    )
    {
        $this->lang = $lang;
        $this->validate = $validate;
    }

    /**
     * @throws Core\Exceptions\InvalidFormToken
     */
    public function validateFormKey()
    {
        if ($this->validate->formToken() === false) {
            throw new Core\Exceptions\InvalidFormToken($this->lang->t('system', 'form_already_submitted'));
        }
    }

    /**
     * @throws Core\Exceptions\ValidationFailed
     */
    protected function _checkForFailedValidation()
    {
        if (!empty($this->errors)) {
            throw new Core\Exceptions\ValidationFailed($this->errors);
        }
    }
}