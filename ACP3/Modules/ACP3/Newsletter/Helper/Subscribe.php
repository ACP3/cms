<?php
namespace ACP3\Modules\ACP3\Newsletter\Helper;


use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Model;

/**
 * Class Subscribe
 * @package ACP3\Modules\ACP3\Newsletter\Helper
 */
class Subscribe
{
    const ACCOUNT_STATUS_CONFIRMATION_NEEDED = 0;
    const ACCOUNT_STATUS_CONFIRMED = 1;
    const ACCOUNT_STATUS_DISABLED = 2;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Mailer
     */
    protected $mailer;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model
     */
    protected $newsletterModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\Date                     $date
     * @param \ACP3\Core\Lang                     $lang
     * @param \ACP3\Core\Mailer                   $mailer
     * @param \ACP3\Core\Http\Request             $request
     * @param \ACP3\Core\Router                   $router
     * @param \ACP3\Core\Helpers\StringFormatter  $stringFormatter
     * @param \ACP3\Core\Helpers\Secure           $secureHelper
     * @param \ACP3\Core\Config                   $config
     * @param \ACP3\Modules\ACP3\Newsletter\Model $newsletterModel
     */
    public function __construct(
        Core\Date $date,
        Core\Lang $lang,
        Core\Mailer $mailer,
        Core\Http\Request $request,
        Core\Router $router,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Helpers\Secure $secureHelper,
        Core\Config $config,
        Model $newsletterModel)
    {
        $this->date = $date;
        $this->lang = $lang;
        $this->mailer = $mailer;
        $this->request = $request;
        $this->router = $router;
        $this->stringFormatter = $stringFormatter;
        $this->secureHelper = $secureHelper;
        $this->config = $config;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * Meldet eine E-Mail-Adresse beim Newsletter an
     *
     * @param string $emailAddress
     * @param int    $salutation
     * @param string $firstName
     * @param string $lastName
     *
     * @return bool
     */
    public function subscribeToNewsletter($emailAddress, $salutation = 0, $firstName = '', $lastName = '')
    {
        $hash = $this->secureHelper->generateSaltedPassword('', mt_rand(0, microtime(true)), 'sha512');
        $mailSent = $this->sendDoubleOptInEmail($emailAddress, $hash);
        $bool = false;

        if ($mailSent === true) {
            $bool = $this->addNewsletterAccount($emailAddress, $salutation, $firstName, $lastName, $hash);
        }

        return $mailSent === true && $bool !== false;
    }

    /**
     * @param string $emailAddress
     * @param int    $salutation
     * @param string $firstName
     * @param string $lastName
     * @param string $hash
     *
     * @return bool|int
     */
    protected function addNewsletterAccount($emailAddress, $salutation, $firstName, $lastName, $hash)
    {
        $newsletterAccount = $this->newsletterModel->getOneByEmail($emailAddress);

        if (!empty($newsletterAccount)) {
            $accountId = $this->updateExistingAccount($newsletterAccount, $salutation, $firstName, $lastName, $hash);
        } else {
            $accountId = $this->insertNewAccount($emailAddress, $salutation, $firstName, $lastName, $hash);
        }

        $historyInsertValues = [
            'newsletter_account_id' => $accountId,
            'date' => $this->date->toSQL(),
            'action' => 1
        ];
        $this->newsletterModel->insert($historyInsertValues, Model::TABLE_NAME_ACCOUNT_HISTORY);

        return $accountId;
    }

    /**
     * @param string $emailAddress
     * @param string $hash
     *
     * @return bool
     */
    protected function sendDoubleOptInEmail($emailAddress, $hash)
    {
        $url = $this->router->route('newsletter/index/activate/hash_' . $hash, true);

        $seoSettings = $this->config->getSettings('seo');
        $settings = $this->config->getSettings('newsletter');

        $subject = sprintf($this->lang->t('newsletter', 'subscribe_mail_subject'), $seoSettings['title']);
        $body = str_replace('{host}', $this->request->getHostname(), $this->lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";

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

        return $this->mailer->send();
    }

    /**
     * @param array  $newsletterAccount
     * @param int    $salutation
     * @param string $firstName
     * @param string $lastName
     * @param string $hash
     *
     * @return int
     */
    protected function updateExistingAccount(array $newsletterAccount, $salutation, $firstName, $lastName, $hash)
    {
        $updateValues = [
            'salutation' => $salutation,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'hash' => $hash,
            'status' => static::ACCOUNT_STATUS_CONFIRMATION_NEEDED
        ];

        $this->newsletterModel->update($updateValues, $newsletterAccount['id'], Model::TABLE_NAME_ACCOUNTS);

        return $newsletterAccount['id'];
    }

    /**
     * @param string $emailAddress
     * @param int    $salutation
     * @param string $firstName
     * @param string $lastName
     * @param string $hash
     *
     * @return bool|int
     */
    protected function insertNewAccount($emailAddress, $salutation, $firstName, $lastName, $hash)
    {
        $insertValues = [
            'id' => '',
            'mail' => $emailAddress,
            'salutation' => $salutation,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'hash' => $hash,
            'status' => static::ACCOUNT_STATUS_CONFIRMATION_NEEDED
        ];
        return $this->newsletterModel->insert($insertValues, Model::TABLE_NAME_ACCOUNTS);
    }
}