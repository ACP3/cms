<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core;

abstract class AbstractFormValidation
{
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     * @param \ACP3\Core\Validation\Validator $validator
     */
    public function __construct(
        Core\I18n\TranslatorInterface $translator,
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
