<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class CaptchaValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    protected $user;
    /**
     * @var CaptchaExtensionInterface
     */
    private $captcha;

    /**
     * CaptchaValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel $user
     * @param CaptchaExtensionInterface                $captcha
     */
    public function __construct(
        UserModel $user,
        CaptchaExtensionInterface $captcha = null
    ) {
        $this->user = $user;
        $this->captcha = $captcha;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = []): bool
    {
        if ($this->user->isAuthenticated() === false) {
            return $this->captcha->isCaptchaValid($data, $field, $extra);
        }

        return true;
    }
}
