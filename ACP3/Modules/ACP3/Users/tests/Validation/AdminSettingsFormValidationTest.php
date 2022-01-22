<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core\Session\SessionConstants;
use ACP3\Core\Validation\AbstractFormValidationTest;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;

/**
 * Class AdminSettingsFormValidationTest.
 */
class AdminSettingsFormValidationTest extends AbstractFormValidationTest
{
    protected function registerValidationRules(): void
    {
        $this->container->set(FormTokenValidationRule::class, $this->setUpFormTokenRule());
        $this->container->set(InArrayValidationRule::class, new InArrayValidationRule());
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());
    }

    protected function initializeFormValidation(): void
    {
        $this->formValidation = new AdminSettingsFormValidation(
            $this->translatorMock,
            $this->validator
        );
    }

    /**
     * {@inheritDoc}
     */
    public function validFormDataProvider(): array
    {
        return [
            [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'enable_registration' => 1,
            ],
            [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'enable_registration' => 0,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidFormDataProvider(): array
    {
        return [
            [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'baz',
                'enable_registration' => '',
            ],
        ];
    }
}
