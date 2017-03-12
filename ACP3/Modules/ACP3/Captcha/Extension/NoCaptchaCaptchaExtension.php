<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class NoCaptchaCaptchaExtension extends ReCaptchaCaptchaExtension
{
    const TEMPLATE = 'Captcha/Partials/captcha_nocaptcha.tpl';

    /**
     * @var Translator
     */
    private $translator;

    /**
     * NoCaptchaCaptchaExtension constructor.
     * @param Translator $translator
     * @param RequestInterface $request
     * @param SettingsInterface $settings
     * @param View $view
     * @param UserModel $user
     */
    public function __construct(
        Translator $translator,
        RequestInterface $request,
        SettingsInterface $settings,
        View $view,
        UserModel $user
    ) {
        parent::__construct($translator, $request, $settings, $view, $user);

        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function getCaptchaName()
    {
        return $this->translator->t('captcha', 'recaptcha_invisible');
    }
}
