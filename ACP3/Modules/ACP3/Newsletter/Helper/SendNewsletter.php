<?php

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository;

/**
 * Class SendNewsletter
 * @package ACP3\Modules\ACP3\Newsletter\Helper
 */
class SendNewsletter
{
    /**
     * @var \ACP3\Core\Mailer
     */
    protected $mailer;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * SendNewsletter constructor.
     *
     * @param \ACP3\Core\Mailer                                        $mailer
     * @param \ACP3\Core\RouterInterface                               $router
     * @param \ACP3\Core\Config                                        $config
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository $newsletterRepository
     */
    public function __construct(
        Core\Mailer $mailer,
        Core\RouterInterface $router,
        Core\Config $config,
        NewsletterRepository $newsletterRepository)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->config = $config;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * Versendet einen Newsletter
     *
     * @param int  $newsletterId
     * @param string|array $recipients
     * @param bool $bcc
     *
     * @return bool
     */
    public function sendNewsletter($newsletterId, $recipients, $bcc = false)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $newsletter = $this->newsletterRepository->getOneById($newsletterId);
        $sender = [
            'email' => $settings['mail'],
            'name' => $this->config->getSettings('seo')['title']
        ];

        $this->mailer
            ->reset()
            ->setBcc($bcc)
            ->setFrom($sender)
            ->setSubject($newsletter['title'])
            ->setUrlWeb($this->router->route('newsletter/archive/details/id_' . $newsletterId, true))
            ->setMailSignature($settings['mailsig']);

        if ($newsletter['html'] == 1) {
            $this->mailer->setTemplate('newsletter/email.tpl');
            $this->mailer->setHtmlBody($newsletter['text']);
        } else {
            $this->mailer->setBody($newsletter['text']);
        }

        $this->mailer->setRecipients($recipients);

        return $this->mailer->send();
    }
}
