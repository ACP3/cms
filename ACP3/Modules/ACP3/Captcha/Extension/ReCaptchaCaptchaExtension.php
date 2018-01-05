<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use ReCaptcha\ReCaptcha;

class ReCaptchaCaptchaExtension implements CaptchaExtensionInterface
{
    const TEMPLATE = 'Captcha/Partials/captcha_recaptcha.tpl';
    /**
     * @var TranslatorInterface
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
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * ReCaptchaCaptchaExtension constructor.
     * @param TranslatorInterface $translator
     * @param RequestInterface $request
     * @param SettingsInterface $settings
     * @param View $view
     * @param UserModel $user
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestInterface $request,
        SettingsInterface $settings,
        View $view,
        UserModel $user
    ) {
        $this->translator = $translator;
        $this->view = $view;
        $this->user = $user;
        $this->settings = $settings;
        $this->request = $request;
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
            $settings = $this->settings->getSettings(Schema::MODULE_NAME);

            $this->view->assign('captcha', [
                'id' => $formFieldId,
                'input_only' => $inputOnly,
                'length' => $captchaLength,
                'sitekey' => $settings['recaptcha_sitekey'],
            ]);

            return $this->view->fetchTemplate(static::TEMPLATE);
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function isCaptchaValid($formData, $formFieldName, array $extra = [])
    {
        if (empty($formData['g-recaptcha-response'])) {
            return false;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $recaptcha = new ReCaptcha($settings['recaptcha_secret']);
        $response = $recaptcha->verify(
            $formData['g-recaptcha-response'],
            $this->request->getSymfonyRequest()->getClientIp()
        );

        return $response->isSuccess();
    }
}
