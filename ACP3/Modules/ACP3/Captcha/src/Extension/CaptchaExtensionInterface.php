<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

interface CaptchaExtensionInterface
{
    public const CAPTCHA_DEFAULT_LENGTH = 5;
    public const CAPTCHA_DEFAULT_INPUT_ID = 'captcha';

    public function getCaptchaName(): string;

    /**
     * Creates and returns the view of the captcha.
     */
    public function getCaptcha(
        int $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        string $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        bool $inputOnly = false,
        string $path = ''
    ): string;

    /**
     * Checks, whether the typed in captcha is valid.
     *
     * @param mixed $formData
     */
    public function isCaptchaValid($formData, string $formFieldName, array $extra = []): bool;
}
