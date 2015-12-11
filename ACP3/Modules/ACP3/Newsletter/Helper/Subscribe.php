<?php
namespace ACP3\Modules\ACP3\Newsletter\Helper;


use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Model\AccountRepository;

/**
 * Class Subscribe
 * @package ACP3\Modules\ACP3\Newsletter\Helper
 */
class Subscribe
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
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
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $accountRepository;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;

    /**
     * @param \ACP3\Core\Date                                       $date
     * @param \ACP3\Core\I18n\Translator                            $translator
     * @param \ACP3\Core\Mailer                                     $mailer
     * @param \ACP3\Core\Http\Request                               $request
     * @param \ACP3\Core\Router                                     $router
     * @param \ACP3\Core\Helpers\StringFormatter                    $stringFormatter
     * @param \ACP3\Core\Helpers\Secure                             $secureHelper
     * @param \ACP3\Core\Config                                     $config
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus    $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository $accountRepository
     */
    public function __construct(
        Core\Date $date,
        Core\I18n\Translator $translator,
        Core\Mailer $mailer,
        Core\Http\Request $request,
        Core\Router $router,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Helpers\Secure $secureHelper,
        Core\Config $config,
        AccountStatus $accountStatusHelper,
        AccountRepository $accountRepository)
    {
        $this->date = $date;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->request = $request;
        $this->router = $router;
        $this->stringFormatter = $stringFormatter;
        $this->secureHelper = $secureHelper;
        $this->config = $config;
        $this->accountStatusHelper = $accountStatusHelper;
        $this->accountRepository = $accountRepository;
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
        $newsletterAccount = $this->accountRepository->getOneByEmail($emailAddress);

        if (!empty($newsletterAccount)) {
            $accountId = $this->updateExistingAccount($newsletterAccount, $salutation, $firstName, $lastName, $hash);
        } else {
            $accountId = $this->insertNewAccount($emailAddress, $salutation, $firstName, $lastName, $hash);
        }

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

        $subject = $this->translator->t('newsletter', 'subscribe_mail_subject', ['%title%' => $seoSettings['title']]);
        $body = $this->translator->t('newsletter', 'subscribe_mail_body',
                ['{host}' => $this->request->getHostname()]) . "\n\n";

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
            'hash' => $hash
        ];

        if ($newsletterAccount['status'] != AccountStatus::ACCOUNT_STATUS_CONFIRMED) {
            $updateValues['status'] = AccountStatus::ACCOUNT_STATUS_CONFIRMATION_NEEDED;
        }

        $this->accountRepository->update($updateValues, $newsletterAccount['id']);

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
            'status' => AccountStatus::ACCOUNT_STATUS_CONFIRMATION_NEEDED
        ];

        return $this->accountRepository->insert($insertValues);
    }
}