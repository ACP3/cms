<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\I18n\Translator;
use Symfony\Component\HttpFoundation\Session\Session;

class NativeCaptchaExtension implements CaptchaExtensionInterface
{
    public function __construct(private Core\ACL $acl, private Translator $translator, private Session $sessionHandler, private Core\View $view, private Core\Helpers\Secure $secureHelper, private UserModelInterface $user)
    {
    }

    public function getCaptchaName(): string
    {
        return $this->translator->t('captcha', 'native');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getCaptcha(
        int $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        string $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        bool $inputOnly = false
    ): string {
        if (!$this->user->isAuthenticated() && $this->hasCaptchaAccess()) {
            $token = sha1((string) mt_rand());

            $this->sessionHandler->set('captcha_' . $token, $this->secureHelper->salt($captchaLength));

            $this->view->assign('captcha', [
                'width' => $captchaLength * 25,
                'id' => $formFieldId,
                'height' => 30,
                'input_only' => $inputOnly,
                'token' => $token,
            ]);

            return $this->view->fetchTemplate('Captcha/Partials/captcha_native.tpl');
        }

        return '';
    }

    private function hasCaptchaAccess(): bool
    {
        return $this->acl->hasPermission('frontend/captcha/index/image') === true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCaptchaValid(mixed $formData, string $formFieldName, array $extra = []): bool
    {
        if (!$this->hasCaptchaAccess()) {
            return true;
        }

        if (!isset($formData[$formFieldName])) {
            return false;
        }

        $value = $formData[$formFieldName];
        $indexName = 'captcha_' . $formData['captcha_token'];

        return preg_match('/^[a-zA-Z0-9]+$/', $value)
            && strtolower($value) === strtolower($this->sessionHandler->get($indexName, ''));
    }
}
