<?php

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Model;

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
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model
     */
    protected $newsletterModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\Mailer                   $mailer
     * @param \ACP3\Core\Router                   $router
     * @param \ACP3\Core\Config                   $config
     * @param \ACP3\Modules\ACP3\Newsletter\Model $newsletterModel
     */
    public function __construct(
        Core\Mailer $mailer,
        Core\Router $router,
        Core\Config $config,
        Model $newsletterModel)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->config = $config;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * Versendet einen Newsletter
     *
     * @param      $newsletterId
     * @param null $recipients
     * @param bool $bcc
     *
     * @return bool
     */
    public function sendNewsletter($newsletterId, $recipients, $bcc = false)
    {
        $settings = $this->config->getSettings('newsletter');

        $newsletter = $this->newsletterModel->getOneById($newsletterId);
        $from = [
            'email' => $settings['mail'],
            'name' => $this->config->getSettings('seo')['title']
        ];

        $this->mailer
            ->reset()
            ->setBcc($bcc)
            ->setFrom($from)
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
