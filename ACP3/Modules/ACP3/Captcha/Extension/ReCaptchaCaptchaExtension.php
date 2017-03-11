<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;


use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class ReCaptchaCaptchaExtension implements CaptchaExtensionInterface
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var View
     */
    private $view;
    /**
     * @var UserModel
     */
    private $user;

    /**
     * ReCaptchaCaptchaExtension constructor.
     * @param Translator $translator
     * @param View $view
     * @param UserModel $user
     */
    public function __construct(Translator $translator, View $view, UserModel $user)
    {
        $this->translator = $translator;
        $this->view = $view;
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getCaptchaName()
    {
        return $this->translator->t('captcha', 'recaptcha');
    }

    /**
     * @inheritdoc
     */
    public function getCaptcha(
        $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        $inputOnly = false,
        $path = ''
    ) {
        if (!$this->user->isAuthenticated()) {
            $this->view->assign('captcha', [
                'id' => $formFieldId,
                'input_only' => $inputOnly,
                'sitekey' => '',
                'language' => $this->translator->getShortIsoCode()
            ]);

            return $this->view->fetchTemplate('Captcha/Partials/captcha_recaptcha.tpl');
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function isCaptchaValid($formData, $formFieldName, array $extra = [])
    {
        return true;
    }
}
