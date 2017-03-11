<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;


interface CaptchaExtensionInterface
{
    const CAPTCHA_DEFAULT_LENGTH = 5;
    const CAPTCHA_DEFAULT_INPUT_ID = 'captcha';

    /**
     * @return string
     */
    public function getCaptchaName();

    /**
     * Creates and returns the view of the captcha
     *
     * @param integer $captchaLength
     * @param string $formFieldId
     * @param bool $inputOnly
     * @param string $path
     *
     * @return string
     */
    public function getCaptcha(
        $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        $inputOnly = false,
        $path = ''
    );

    /**
     * Returns the name of the to be used validation rule
     *
     * @return string
     */
    public function getValidationRule();
}
