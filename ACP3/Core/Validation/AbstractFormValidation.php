<?php
namespace ACP3\Core\Validation;

use ACP3\Core;

/**
 * Class AbstractFormValidation
 * @package ACP3\Core\Validation
 */
abstract class AbstractFormValidation
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\I18n\Translator      $translator
     * @param \ACP3\Core\Validation\Validator $validator
     */
    public function __construct(
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator
    ) {
        $this->translator = $translator;
        $this->validator = $validator;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    abstract public function validate(array $formData);
}
