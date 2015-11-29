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
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     */
    public function __construct(
        Core\Lang $lang, Core\Validator\Validator $validator, Rules\Misc $validate
    )
    {
        $this->lang = $lang;
        $this->validate = $validate;
        $this->validator = $validator;
    }

    /**
     * @throws Core\Exceptions\InvalidFormToken
     *
     * @deprecated
     */
    public function validateFormKey()
    {
        if ($this->validate->formToken() === false) {
            throw new Core\Exceptions\InvalidFormToken();
        }
    }

    /**
     * @throws Core\Exceptions\ValidationFailed
     *
     * @deprecated
     */
    protected function _checkForFailedValidation()
    {
        if (!empty($this->errors)) {
            throw new Core\Exceptions\ValidationFailed($this->errors);
        }
    }
}