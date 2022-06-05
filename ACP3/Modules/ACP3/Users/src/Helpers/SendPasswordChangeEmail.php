<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Helpers;

use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\Installer\Schema as UserSchema;

class SendPasswordChangeEmail
{
    public function __construct(private readonly RequestInterface $request, private readonly SendEmail $sendEmail, private readonly SettingsInterface $settings, private readonly Translator $translator)
    {
    }

    /**
     * @param array<string, mixed> $user
     */
    public function __invoke(array $user, string $newPassword): bool
    {
        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        $subject = $this->translator->t(
            'users',
            'forgot_pwd_mail_subject',
            [
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );
        $body = $this->translator->t(
            'users',
            'forgot_pwd_mail_message',
            [
                '{name}' => $user['nickname'],
                '{mail}' => $user['mail'],
                '{password}' => $newPassword,
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );

        $settings = $this->settings->getSettings(UserSchema::MODULE_NAME);

        $data = (new MailerMessage())
            ->setRecipients([
                'name' => $user['realname'],
                'email' => $user['mail'],
            ])
            ->setFrom($settings['mail'])
            ->setSubject($subject)
            ->setTemplate('Users/layout.email.forgot_pwd.tpl')
            ->setBody($body);

        return $this->sendEmail->execute($data);
    }
}
