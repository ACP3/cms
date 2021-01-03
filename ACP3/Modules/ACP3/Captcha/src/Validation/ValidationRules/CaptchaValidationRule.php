<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Validation\ValidationRules;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;

class CaptchaValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var CaptchaExtensionInterface
     */
    private $captcha;

    public function __construct(
        UserModelInterface $user,
        CaptchaExtensionInterface $captcha = null
    ) {
        $this->user = $user;
        $this->captcha = $captcha;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if ($this->user->isAuthenticated() === false) {
            return $this->captcha->isCaptchaValid($data, $field, $extra);
        }

        return true;
    }
}
