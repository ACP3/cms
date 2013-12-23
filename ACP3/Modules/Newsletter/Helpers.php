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
        self::$model = new Model(Core\Registry::get('Db'));
    }

    /**
     * Versendet einen Newsletter
     *
     * @param string $subject
     * @param string $body
     * @param string $from_address
     * @return boolean
     */
    public static function sendNewsletter($subject, $body, $from_address)
    {
        self::_init();

        $accounts = self::$model->getAllAccounts();
        $c_accounts = count($accounts);

        for ($i = 0; $i < $c_accounts; ++$i) {
            if (!empty($accounts[$i]['hash'])) {
                continue;
            }

            $bool2 = Core\Functions::generateEmail('', $accounts[$i]['mail'], $from_address, $subject, $body);
            if ($bool2 === false) {
                return false;
            }
        }
        return true;
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
        $mail_sent = Core\Functions::generateEmail('', $emailAddress, $settings['mail'], $subject, $body);
        $bool = false;

        // Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
        if ($mail_sent === true) {
            $insertValues = array(
                'id' => '',
                'mail' => $emailAddress,
                'hash' => $hash
            );
            $bool = self::$model->insert($insertValues, Model::TABLE_NAME_ACCOUNTS);
        }

        return $mail_sent === true && $bool !== false;
    }

}