<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ReCaptcha\ReCaptcha;

class ReCaptchaCaptchaExtension implements CaptchaExtensionInterface
{
    private const TEMPLATE = 'Captcha/Partials/captcha_recaptcha.tpl';

    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var View
     */
    private $view;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
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
     * @var bool
     */
    private $includeJsAssets = true;

    public function __construct(
        Translator $translator,
        RequestInterface $request,
        SettingsInterface $settings,
        View $view,
        UserModelInterface $user
    ) {
        $this->translator = $translator;
        $this->view = $view;
        $this->user = $user;
        $this->settings = $settings;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptchaName(): string
    {
        return $this->translator->t('captcha', 'recaptcha');
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptcha(
        int $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        string $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        bool $inputOnly = false,
        string $path = ''
    ): string {
        if (!$this->user->isAuthenticated()) {
            $settings = $this->settings->getSettings(Schema::MODULE_NAME);

            $this->view->assign('captcha', [
                'id' => $formFieldId,
                'input_only' => $inputOnly,
                'length' => $captchaLength,
                'sitekey' => $settings['recaptcha_sitekey'],
                'includeJsAssets' => $this->includeJsAssets,
            ]);

            if ($this->includeJsAssets) {
                $this->includeJsAssets = false;
            }

            return $this->view->fetchTemplate(static::TEMPLATE);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isCaptchaValid($formData, string $formFieldName, array $extra = []): bool
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
