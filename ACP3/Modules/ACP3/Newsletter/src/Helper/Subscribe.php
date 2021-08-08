<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository;

class Subscribe
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Mailer
     */
    private $mailer;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    private $stringFormatter;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository
     */
    private $accountRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $config;

    public function __construct(
        Core\I18n\Translator $translator,
        Core\Mailer $mailer,
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Core\Helpers\StringFormatter $stringFormatter,
        Core\Helpers\Secure $secureHelper,
        Core\Settings\SettingsInterface $config,
        AccountRepository $accountRepository
    ) {
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->request = $request;
        $this->router = $router;
        $this->stringFormatter = $stringFormatter;
        $this->secureHelper = $secureHelper;
        $this->config = $config;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Meldet eine E-Mail-Adresse beim Newsletter an.
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function subscribeToNewsletter(string $emailAddress, int $salutation = 0, string $firstName = '', string $lastName = ''): bool
    {
        $hash = $this->secureHelper->generateSaltedPassword('', (string) random_int(0, (int) microtime(true)), 'sha512');
        $mailSent = $this->sendDoubleOptInEmail($emailAddress, $hash);
        $result = $this->addNewsletterAccount($emailAddress, $salutation, $firstName, $lastName, $hash);

        return $mailSent === true && $result !== false;
    }

    /**
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function addNewsletterAccount(string $emailAddress, int $salutation, string $firstName, string $lastName, string $hash)
    {
        $newsletterAccount = $this->accountRepository->getOneByEmail($emailAddress);

        if (!empty($newsletterAccount)) {
            $accountId = $this->updateExistingAccount($newsletterAccount, $salutation, $firstName, $lastName, $hash);
        } else {
            $accountId = $this->insertNewAccount($emailAddress, $salutation, $firstName, $lastName, $hash);
        }

        return $accountId;
    }

    protected function sendDoubleOptInEmail(string $emailAddress, string $hash): bool
    {
        $url = $this->router->route('newsletter/index/activate/hash_' . $hash, true);

        $systemSettings = $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME);
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $body = $this->translator->t(
            'newsletter',
            'subscribe_mail_body',
            ['{host}' => $this->request->getHost()]
        );
        $body .= "\n\n";

        $data = (new Core\Mailer\MailerMessage())
            ->setFrom([
                'email' => $settings['mail'],
                'name' => $systemSettings['site_title'],
            ])
            ->setSubject($this->translator->t(
                'newsletter',
                'subscribe_mail_subject',
                ['%title%' => $systemSettings['site_title']]
            ))
            ->setMailSignature($settings['mailsig'])
            ->setRecipients($emailAddress);

        if ($settings['html'] == 1) {
            $data->setTemplate('newsletter/layout.email.subscribe.tpl');

            $body .= '<a href="' . $url . '">' . $url . '<a>';
            $data->setHtmlBody($this->stringFormatter->nl2p($body));
        } else {
            $body .= $url;
            $data->setBody($body);
        }

        return $this->mailer
            ->reset()
            ->send($data);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function updateExistingAccount(array $newsletterAccount, int $salutation, string $firstName, string $lastName, string $hash): int
    {
        $updateValues = [
            'salutation' => $salutation,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'hash' => $hash,
        ];

        if ($newsletterAccount['status'] != AccountStatus::ACCOUNT_STATUS_CONFIRMED) {
            $updateValues['status'] = AccountStatus::ACCOUNT_STATUS_CONFIRMATION_NEEDED;
        }

        $this->accountRepository->update($updateValues, $newsletterAccount['id']);

        return $newsletterAccount['id'];
    }

    /**
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function insertNewAccount(string $emailAddress, int $salutation, string $firstName, string $lastName, string $hash)
    {
        $insertValues = [
            'mail' => $emailAddress,
            'salutation' => $salutation,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'hash' => $hash,
            'status' => AccountStatus::ACCOUNT_STATUS_CONFIRMATION_NEEDED,
        ];

        return $this->accountRepository->insert($insertValues);
    }
}
