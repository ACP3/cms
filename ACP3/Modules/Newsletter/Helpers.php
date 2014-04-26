<?php

/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

abstract class Helpers
{

    /**
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(
                Core\Registry::get('Db'),
                Core\Registry::get('Lang'),
                Core\Registry::get('Auth')
            );
        }
    }

    /**
     * Versendet einen Newsletter
     *
     * @param $newsletterId
     * @param null $recipient
     * @param bool $bcc
     * @return bool
     */
    public static function sendNewsletter($newsletterId, $recipient = null, $bcc = false)
    {
        self::_init();

        $settings = Core\Config::getSettings('newsletter');
        $newsletter = self::$model->getOneById($newsletterId);

        $mailer = new Core\Mailer(Core\Registry::get('View'));
        $mailer
            ->setFrom($settings['mail'])
            ->setSubject($newsletter['title'])
            ->setMailSignature($settings['mailsig']);

        if ($newsletter['html'] == 1) {
            $mailer->setTemplate('newsletter/email.tpl');
            $mailer->setHtmlBody($newsletter['text']);
        } else {
            $mailer->setBody($newsletter['text']);
        }

        if ($recipient !== null) {
            if ($bcc === true) {
                $mailer->setBcc($recipient);
            } else {
                $mailer->setTo($recipient);
            }
        } else {
            $accounts = self::$model->getAllActiveAccount();
            $c_accounts = count($accounts);
            $recipients = array();

            for ($i = 0; $i < $c_accounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            if ($bcc === true) {
                $mailer->setBcc($recipients);
            } else {
                $mailer->setTo($recipients);
            }
        }

        return $mailer->send();
    }

    /**
     * Meldet eine E-Mail-Adresse beim Newsletter an
     *
     * @param string $emailAddress
     *    Die anzumeldente E-Mail-Adresse
     * @return boolean
     */
    public static function subscribeToNewsletter($emailAddress)
    {
        self::_init();

        $hash = md5(mt_rand(0, microtime(true)));
        $host = htmlentities($_SERVER['HTTP_HOST']);
        $settings = Core\Config::getSettings('newsletter');

        $subject = sprintf(Core\Registry::get('Lang')->t('newsletter', 'subscribe_mail_subject'), CONFIG_SEO_TITLE);
        $body = str_replace('{host}', $host, Core\Registry::get('Lang')->t('newsletter', 'subscribe_mail_body')) . "\n\n";
        $body .= 'http://' . $host . Core\Registry::get('URI')->route('newsletter/activate/hash_' . $hash . '/mail_' . $emailAddress);
        $mailSent = Core\Functions::generateEmail('', $emailAddress, $settings['mail'], $subject, $body);
        $bool = false;

        // Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
        if ($mailSent === true) {
            $insertValues = array(
                'id' => '',
                'mail' => $emailAddress,
                'hash' => $hash
            );
            $bool = self::$model->insert($insertValues, Model::TABLE_NAME_ACCOUNTS);
        }

        return $mailSent === true && $bool !== false;
    }

}