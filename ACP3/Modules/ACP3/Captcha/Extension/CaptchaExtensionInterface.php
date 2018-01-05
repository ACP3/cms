<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * Checks, whether the typed in captcha is valid
     *
     * @param mixed $formData
     * @param string $formFieldName
     * @param array $extra
     * @return bool
     */
    public function isCaptchaValid($formData, $formFieldName, array $extra = []);
}
