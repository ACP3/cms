<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Model;


use ACP3\Core\Config;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Contact\Installer\Schema;

/**
 * Class ContactFormModel
 * @package ACP3\Modules\ACP3\Contact\Model
 */
class ContactFormModel
{
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var SendEmail
     */
    protected $sendEmail;

    /**
     * ContactFormModel constructor.
     * @param Config $config
     * @param Translator $translator
     * @param Secure $secure
     * @param SendEmail $sendEmail
     */
    public function __construct(
        Config $config,
        Translator $translator,
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
        $seoSettings = $this->getSeoSettings();
        $settings = $this->getContactSettings();

        $subject = $this->buildSubject('contact_subject', $seoSettings['title']);
        $body = $this->buildEmailBody($formData, 'contact_body');

        return $this->sendEmail->execute(
            '',
            $settings['mail'],
            $formData['mail'],
            $subject,
            $body
        );
    }

    /**
     * @return array
     */
    protected function getSeoSettings()
    {
        return $this->config->getSettings(\ACP3\Modules\ACP3\Seo\Installer\Schema::MODULE_NAME);
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
                '%message%' => $this->secure->strEncode($formData['message'], true)
            ]
        );
    }

    /**
     * @param array $formData
     * @return bool
     */
    protected function sendEmailCopy(array $formData)
    {
        $seoSettings = $this->getSeoSettings();
        $settings = $this->getContactSettings();

        $subject = $this->buildSubject('sender_subject', $seoSettings['title']);
        $body = $this->buildEmailBody($formData, 'sender_body');

        return $this->sendEmail->execute(
            $formData['name'],
            $formData['mail'],
            $settings['mail'],
            $subject,
            $body
        );
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
