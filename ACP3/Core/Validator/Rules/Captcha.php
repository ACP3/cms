<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule;

/**
 * Class Captcha
 * @package ACP3\Core\Validator\Rules
 *
 * @deprecated
 */
class Captcha
{
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule
     */
    protected $captchaValidationRule;

    /**
     * Captcha constructor.
     *
     * @param \ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule $captchaValidationRule
     */
    public function __construct(CaptchaValidationRule $captchaValidationRule)
    {
        $this->captchaValidationRule = $captchaValidationRule;
    }

    /**
     * @param string $input
     * @param string $path
     *
     * @return boolean
     *
     * @deprecated
     */
    public function captcha($input, $path = '')
    {
        return $this->captchaValidationRule->isValid($input, '', ['path' => $path]);
    }
}
