<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\I18n\LocaleInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class RenderRecaptchaOnLayoutContentBeforeListener
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var View
     */
    private $view;
    /**
     * @var UserModel
     */
    private $userModel;
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * RenderRecaptchaOnLayoutContentBeforeListener constructor.
     * @param LocaleInterface $locale
     * @param SettingsInterface $settings
     * @param View $view
     * @param UserModel $userModel
     */
    public function __construct(
        LocaleInterface $locale,
        SettingsInterface $settings,
        View $view,
        UserModel $userModel)
    {
        $this->settings = $settings;
        $this->view = $view;
        $this->userModel = $userModel;
        $this->locale = $locale;
    }

    public function renderRecaptcha()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($this->isRecaptcha($settings)) {
            $this->view->assign('recaptcha', [
                'sitekey' => $settings['recaptcha_sitekey'],
                'lang' => $this->locale->getShortIsoCode()
            ]);
            $this->view->displayTemplate($this->getServiceIdTemplateMap()[$settings['captcha']]);
        }
    }

    /**
     * @return array
     */
    private function getServiceIdTemplateMap()
    {
        return [
            'captcha.extension.recaptcha_captcha_extension' => 'Captcha/Partials/captcha_recaptcha.onload.tpl',
        ];
    }

    /**
     * @param array $settings
     * @return bool
     */
    private function isRecaptcha(array $settings)
    {
        return !empty($settings)
            && array_key_exists($settings['captcha'], $this->getServiceIdTemplateMap())
            && !empty($settings['recaptcha_sitekey'])
            && !empty($settings['recaptcha_secret']);
    }
}
