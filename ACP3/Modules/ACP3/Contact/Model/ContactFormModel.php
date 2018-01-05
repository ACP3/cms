<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Model;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Contact\Installer\Schema;

class ContactFormModel
{
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var SendEmail
     */
    protected $sendEmail;

    /**
     * ContactFormModel constructor.
     * @param SettingsInterface $config
     * @param TranslatorInterface $translator
     * @param Secure $secure
     * @param SendEmail $sendEmail
     */
    public function __construct(
        SettingsInterface $config,
        TranslatorInterface $translator,
        Secure $secure,
        SendEmail $sendEmail
    ) {
        $this->config = $config;
        $this->translator = $translator;
        $this->secure = $secure;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param array $formData
     * @return bool
     */
    public function sendContactFormEmail(array $formData)
    {
        return $this->sendEmail($formData);
    }

    /**
     * @param array $formData
     * @return bool
     */
    protected function sendEmail(array $formData)
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
                'name' => $formData['name'],
                'email' => $formData['mail'],
            ])
            ->setSender($settings['mail']);

        return $this->sendEmail->execute($data);
    }

    /**
     * @return array
     */
    protected function getSystemSettings()
    {
        return $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME);
    }

    /**
     * @return array
     */
    protected function getContactSettings()
    {
        return $this->config->getSettings(Schema::MODULE_NAME);
    }

    /**
     * @param string $phrase
     * @param string $siteTitle
     * @return string
     */
    protected function buildSubject($phrase, $siteTitle)
    {
        return $this->translator->t('contact', $phrase, ['%title%' => $siteTitle]);
    }

    /**
     * @param array $formData
     * @param string $phrase
     * @return string
     */
    protected function buildEmailBody(array $formData, $phrase)
    {
        return $this->translator->t(
            'contact',
            $phrase,
            [
                '%name%' => $formData['name'],
                '%mail%' => $formData['mail'],
                '%message%' => $this->secure->strEncode($formData['message'], true),
            ]
        );
    }

    /**
     * @param array $formData
     * @return bool
     */
    protected function sendEmailCopy(array $formData)
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
     * @param array $formData
     * @return bool
     */
    public function sendContactFormEmailCopy(array $formData)
    {
        return $this->sendEmailCopy($formData);
    }
}
