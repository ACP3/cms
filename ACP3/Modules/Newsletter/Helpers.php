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
     * @var Core\Router
     */
    protected $router;
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var Model
     */
    protected $newsletterModel;
    /**
     * @var Core\Config
     */
    protected $newsletterConfig;

    public function __construct(
        Core\Lang $lang,
        Core\Router $router,
        Core\View $view,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Config $newsletterConfig,
        Model $newsletterModel
    )
    {
        $this->lang = $lang;
        $this->router = $router;
        $this->view = $view;
        $this->stringFormatter = $stringFormatter;
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
        $from = array(
            'email' => $settings['mail'],
            'name' => CONFIG_SEO_TITLE
        );

        $mailer = new Core\Mailer($this->view, $bcc);
        $mailer
            ->setFrom($from)
            ->setSubject($newsletter['title'])
            ->setUrlWeb(HOST_NAME . $this->router->route('newsletter/archive/details/id_' . $newsletterId))
            ->setMailSignature($settings['mailsig']);

        if ($newsletter['html'] == 1) {
            $mailer->setTemplate('newsletter/email.tpl');
            $mailer->setHtmlBody($newsletter['text']);
        } else {
            $mailer->setBody($newsletter['text']);
        }

        $mailer->setRecipients($recipients);

        return $mailer->send();
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

        $settings = $this->newsletterConfig->getSettings();

        $subject = sprintf($this->lang->t('newsletter', 'subscribe_mail_subject'), CONFIG_SEO_TITLE);
        $body = str_replace('{host}', $host, $this->lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";

        $from = array(
            'email' => $settings['mail'],
            'name' => CONFIG_SEO_TITLE
        );

        $mailer = new Core\Mailer($this->view);
        $mailer
            ->setFrom($from)
            ->setSubject($subject)
            ->setMailSignature($settings['mailsig']);

        if ($settings['html'] == 1) {
            $mailer->setTemplate('newsletter/email.tpl');

            $body .= '<a href="' . $url . '">' . $url . '<a>';
            $mailer->setHtmlBody($this->stringFormatter->nl2p($body));
        } else {
            $body .= $url;
            $mailer->setBody($body);
        }

        $mailer->setRecipients($emailAddress);

        $mailSent = $mailer->send();
        $bool = false;

        // Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
        if ($mailSent === true) {
            $insertValues = array(
                'id' => '',
                'mail' => $emailAddress,
                'hash' => $hash
            );
            $bool = $this->newsletterModel->insert($insertValues, Model::TABLE_NAME_ACCOUNTS);
        }

        return $mailSent === true && $bool !== false;
    }

}