<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 * Class AdminSettingsFormValidationTest
 */
class AdminSettingsFormValidationTest extends AbstractFormValidationTest
{
    /**
     * @return void
     */
    protected function registerValidationRules()
    {
        $this->validator->registerValidationRule($this->setUpFormTokenRule());

        $inArrayRule = new \ACP3\Core\Validation\ValidationRules\InArrayValidationRule();
        $this->validator->registerValidationRule($inArrayRule);

        $emailRule = new \ACP3\Core\Validation\ValidationRules\EmailValidationRule();
        $this->validator->registerValidationRule($emailRule);
    }

    /**
     * @return void
     */
    protected function initializeFormValidation()
    {
        $this->formValidation = new \ACP3\Modules\ACP3\Users\Validation\AdminSettingsFormValidation(
            $this->translatorMock,
            $this->validator
        );
    }

    /**
     * @return array
     */
    public function validFormDataProvider()
    {
        return [
            [
                \ACP3\Core\SessionHandler::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'language_override' => 1,
                'entries_override' => 1,
                'enable_registration' => 1
            ],
            [
                \ACP3\Core\SessionHandler::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'language_override' => 0,
                'entries_override' => 0,
                'enable_registration' => 0
            ]
        ];
    }

    /**
     * @return array
     */
    public function invalidFormDataProvider()
    {
        return [
            [
                \ACP3\Core\SessionHandler::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'baz',
                'language_override' => 'foo',
                'entries_override' => 'bar',
                'enable_registration' => ''
            ],
        ];
    }
}
