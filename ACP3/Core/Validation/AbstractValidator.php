<?php
namespace ACP3\Core\Validation;

use ACP3\Core;

/**
 * Class AbstractValidator
 * @package ACP3\Core\Validation
 */
class AbstractValidator
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validation\Validator $validator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validation\Validator $validator
    )
    {
        $this->lang = $lang;
        $this->validator = $validator;
    }

    /**
     * @throws Core\Exceptions\InvalidFormToken
     *
     * @deprecated
     */
    public function validateFormKey()
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME);
    }

    /**
     * @throws Core\Exceptions\ValidationFailed
     *
     * @deprecated
     */
    protected function _checkForFailedValidation()
    {
        $this->validator->validate();

        if (!empty($this->errors)) {
            throw new Core\Exceptions\ValidationFailed($this->errors);
        }
    }
}