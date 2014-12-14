<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Newsletter
 */
class Helpers
{
    /**
     * @var Core\Lang
     */
    protected $lang;
    /**
     * @var Core\Mailer
     */
    protected $mailer;
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var Core\Config
     */
    protected $seoConfig;
    /**
     * @var Model
     */
    protected $newsletterModel;
    /**
     * @var Core\Config
     */
    protected $newsletterConfig;

    /**
     * @param Core\Lang $lang
     * @param Core\Mailer $mailer
     * @param Core\Router $router
     * @param Core\Helpers\StringFormatter $stringFormatter
     * @param Core\Config $seoConfig
     * @param Core\Config $newsletterConfig
     * @param Model $newsletterModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Mailer $mailer,
        Core\Router $router,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Config $seoConfig,
        Core\Config $newsletterConfig,
        Model $newsletterModel) {
        $this->lang = $lang;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->stringFormatter = $stringFormatter;
        $this->seoConfig = $seoConfig;
        $this->newsletterConfig = $newsletterConfig;
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
        $settings = $this->newsletterConfig->getSettings();

        $newsletter = $this->newsletterModel->getOneById($newsletterId);
        $from = [
            'email' => $settings['mail'],
            'name' => $this->seoConfig->getSettings()['title']
        ];

        $this->mailer
            ->reset()
            ->setBcc($bcc)
            ->setFrom($from)
            ->setSubject($newsletter['title'])
            ->setUrlWeb(HOST_NAME . $this->router->route('newsletter/archive/details/id_' . $newsletterId))
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

    /**
     * Meldet eine E-Mail-Adresse beim Newsletter an
     *
     * @param string $emailAddress
     *    Die anzumeldende E-Mail-Adresse
     *
     * @return boolean
     */
    public function subscribeToNewsletter($emailAddress)
    {
        $hash = md5(mt_rand(0, microtime(true)));
        $host = htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES, 'UTF-8');
        $url = 'http://' . $host . $this->router->route('newsletter/index/activate/hash_' . $hash . '/mail_' . $emailAddress);

        $seoSettings = $this->seoConfig->getSettings();
        $settings = $this->newsletterConfig->getSettings();

        $subject = sprintf($this->lang->t('newsletter', 'subscribe_mail_subject'), $seoSettings['title']);
        $body = str_replace('{host}', $host, $this->lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";

        $from = [
            'email' => $settings['mail'],
            'name' => $seoSettings['title']
        ];

        $this->mailer
            ->reset()
            ->setFrom($from)
            ->setSubject($subject)
            ->setMailSignature($settings['mailsig']);

        if ($settings['html'] == 1) {
            $this->mailer->setTemplate('newsletter/email.tpl');

            $body .= '<a href="' . $url . '">' . $url . '<a>';
            $this->mailer->setHtmlBody($this->stringFormatter->nl2p($body));
        } else {
            $body .= $url;
            $this->mailer->setBody($body);
        }

        $this->mailer->setRecipients($emailAddress);

        $mailSent = $this->mailer->send();
        $bool = false;

        // Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
        if ($mailSent === true) {
            $insertValues = [
                'id' => '',
                'mail' => $emailAddress,
                'hash' => $hash
            ];
            $bool = $this->newsletterModel->insert($insertValues, Model::TABLE_NAME_ACCOUNTS);
        }

        return $mailSent === true && $bool !== false;
    }
}
