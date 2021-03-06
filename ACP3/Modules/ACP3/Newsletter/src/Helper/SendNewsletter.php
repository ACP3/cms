<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository;

class SendNewsletter
{
    /**
     * @var \ACP3\Core\Mailer
     */
    protected $mailer;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;

    /**
     * SendNewsletter constructor.
     *
     * @param \ACP3\Core\Mailer                     $mailer
     * @param \ACP3\Core\Router\RouterInterface     $router
     * @param \ACP3\Core\Settings\SettingsInterface $config
     */
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
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function sendNewsletter(int $newsletterId, $recipients, bool $bcc = false)
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
