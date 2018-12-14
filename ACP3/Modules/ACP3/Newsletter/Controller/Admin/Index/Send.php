<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Send extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    protected $newsletterHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository
     */
    protected $accountRepository;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Newsletter\Model\Repository\NewsletterRepository $newsletterRepository,
        Newsletter\Model\Repository\AccountRepository $accountRepository,
        Newsletter\Helper\SendNewsletter $newsletterHelpers
    ) {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
        $this->accountRepository = $accountRepository;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        if ($this->newsletterRepository->newsletterExists($id) === true) {
            $accounts = $this->accountRepository->getAllActiveAccounts();
            $cAccounts = \count($accounts);
            $recipients = [];

            for ($i = 0; $i < $cAccounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $sendNewsletterResult = $this->newsletterHelpers->sendNewsletter($id, $recipients);
            $newsletterUpdateResult = false;
            if ($sendNewsletterResult === true) {
                $newsletterUpdateResult = $this->newsletterRepository->update(['status' => '1'], $id);
            }

            return $this->redirectMessages()->setMessage(
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
