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

            $bool = $this->newsletterHelpers->sendNewsletter($id, $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterRepository->update(['status' => '1'], $id);
            }

            return $this->redirectMessages()->setMessage(
                $bool === true && $bool2 !== false,
                $this->translator->t(
                    'newsletter',
                    $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'
                )
            );
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
