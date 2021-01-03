<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;

class HoneypotCaptchaExtension implements CaptchaExtensionInterface
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var View
     */
    private $view;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $userModel;

    public function __construct(Translator $translator, View $view, UserModelInterface $userModel)
    {
        $this->translator = $translator;
        $this->view = $view;
        $this->userModel = $userModel;
    }

    public function getCaptchaName(): string
    {
        return $this->translator->t('captcha', 'honeypot');
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
        if (!$this->userModel->isAuthenticated()) {
            $this->view->assign('captcha', [
                'id' => $formFieldId,
                'input_only' => $inputOnly,
            ]);

            return $this->view->fetchTemplate('Captcha/Partials/captcha_honeypot.tpl');
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isCaptchaValid($formData, string $formFieldName, array $extra = []): bool
    {
        return empty($formData[$formFieldName]);
    }
}
