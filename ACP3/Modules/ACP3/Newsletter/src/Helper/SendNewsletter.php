<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Repository\NewsletterRepository;

class SendNewsletter
{
    /**
     * @var \ACP3\Core\Mailer
     */
    private $mailer;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Repository\NewsletterRepository
     */
    private $newsletterRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $config;

    public function __construct(
        Core\Mailer $mailer,
        Core\Router\RouterInterface $router,
        Core\Settings\SettingsInterface $config,
        NewsletterRepository $newsletterRepository
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->config = $config;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * Versendet einen Newsletter.
     *
     * @param string|array $recipients
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function sendNewsletter(int $newsletterId, $recipients, bool $bcc = false): bool
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $newsletter = $this->newsletterRepository->getOneById($newsletterId);
        $sender = [
            'email' => $settings['mail'],
            'name' => $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME)['site_title'],
        ];

        $message = (new Core\Mailer\MailerMessage())
            ->setBcc($bcc)
            ->setFrom($sender)
            ->setSubject($newsletter['title'])
            ->setUrlWeb($this->router->route('newsletter/archive/details/id_' . $newsletterId, true))
            ->setMailSignature($settings['mailsig']);

        if ($newsletter['html'] == 1) {
            $message->setTemplate('newsletter/layout.email.tpl');
            $message->setHtmlBody($newsletter['text']);
        } else {
            $message->setBody($newsletter['text']);
        }

        $message->setRecipients($recipients);

        return $this->mailer
            ->reset()
            ->send($message);
    }
}
