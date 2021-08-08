<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Send extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Repository\NewsletterRepository
     */
    private $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    private $newsletterHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository
     */
    private $accountRepository;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Helpers\RedirectMessages $redirectMessages,
        Newsletter\Repository\NewsletterRepository $newsletterRepository,
        Newsletter\Repository\AccountRepository $accountRepository,
        Newsletter\Helper\SendNewsletter $newsletterHelpers
    ) {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
        $this->accountRepository = $accountRepository;
        $this->newsletterHelpers = $newsletterHelpers;
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $id)
    {
        if ($this->newsletterRepository->newsletterExists($id) === true) {
            $accounts = $this->accountRepository->getAllActiveAccounts();
            $recipients = [];

            foreach ($accounts as $i => $account) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $sendNewsletterResult = $this->newsletterHelpers->sendNewsletter($id, $recipients);
            $newsletterUpdateResult = false;
            if ($sendNewsletterResult === true) {
                $newsletterUpdateResult = $this->newsletterRepository->update(['status' => '1'], $id);
            }

            return $this->redirectMessages->setMessage(
                $sendNewsletterResult === true && $newsletterUpdateResult !== false,
                $this->translator->t(
                    'newsletter',
                    $sendNewsletterResult === true && $newsletterUpdateResult !== false
                        ? 'create_success'
                        : 'create_save_error'
                )
            );
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
