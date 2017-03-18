<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Utility;

use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;

class CaptchaRegistrar
{
    /**
     * @var CaptchaExtensionInterface[]
     */
    protected $availableCaptchas = [];

    /**
     * @param string $serviceId
     * @param CaptchaExtensionInterface $captchaExtension
     */
    public function registerCaptcha($serviceId, CaptchaExtensionInterface $captchaExtension)
    {
        $this->availableCaptchas[$serviceId] = $captchaExtension;
    }

    /**
     * @return CaptchaExtensionInterface[]
     */
    public function getAvailableCaptchas()
    {
        return $this->availableCaptchas;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function hasCaptcha($serviceId)
    {
        return isset($this->availableCaptchas[$serviceId]);
    }
}
