<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Model;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Contact\Installer\Schema;

class ContactFormModel
{
    public function __construct(protected SettingsInterface $config, protected Translator $translator, protected Secure $secure, protected SendEmail $sendEmail)
    {
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function sendContactFormEmail(array $formData): bool
    {
        return $this->sendEmail($formData);
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function sendEmail(array $formData): bool
    {
        $systemSettings = $this->getSystemSettings();
        $settings = $this->getContactSettings();

        $subject = $this->buildSubject('contact_subject', $systemSettings['site_title']);
        $body = $this->buildEmailBody($formData, 'contact_body');

        $data = (new MailerMessage())
            ->setSubject($subject)
            ->setBody($body)
            ->setTemplate('Contact/layout.email.tpl')
            ->setRecipients([
                'name' => $systemSettings['site_title'],
                'email' => $settings['mail'],
            ])
            ->setFrom([
                'name' => $systemSettings['site_title'],
                'email' => $settings['mail'],
            ])
            ->setReplyTo([
                'name' => $formData['name'],
                'email' => $formData['mail'],
            ]);

        return $this->sendEmail->execute($data);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSystemSettings(): array
    {
        return $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContactSettings(): array
    {
        return $this->config->getSettings(Schema::MODULE_NAME);
    }

    protected function buildSubject(string $phrase, string $siteTitle): string
    {
        return $this->translator->t('contact', $phrase, ['%title%' => $siteTitle]);
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function buildEmailBody(array $formData, string $phrase): string
    {
        return $this->translator->t(
            'contact',
            $phrase,
            [
                '%name%' => $this->secure->strEncode($formData['name']),
                '%mail%' => $formData['mail'],
                '%message%' => $this->secure->strEncode($formData['message']),
            ]
        );
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function sendEmailCopy(array $formData): bool
    {
        $systemSettings = $this->getSystemSettings();
        $settings = $this->getContactSettings();

        $subject = $this->buildSubject('sender_subject', $systemSettings['site_title']);
        $body = $this->buildEmailBody($formData, 'sender_body');

        $data = (new MailerMessage())
            ->setSubject($subject)
            ->setBody($body)
            ->setTemplate('Contact/layout.email.tpl')
            ->setRecipients([
                'name' => $formData['name'],
                'email' => $formData['mail'],
            ])
            ->setFrom([
                'name' => $systemSettings['site_title'],
                'email' => $settings['mail'],
            ]);

        return $this->sendEmail->execute($data);
    }

    /**
     * @param array<string, mixed> $formData
     */
    public function sendContactFormEmailCopy(array $formData): bool
    {
        return $this->sendEmailCopy($formData);
    }
}
