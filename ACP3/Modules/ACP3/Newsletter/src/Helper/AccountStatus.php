<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountHistoryRepository;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository;

class AccountStatus
{
    public const ACCOUNT_STATUS_CONFIRMATION_NEEDED = 0;
    public const ACCOUNT_STATUS_CONFIRMED = 1;
    public const ACCOUNT_STATUS_DISABLED = 2;

    public function __construct(protected Date $date, protected AccountRepository $accountRepository, protected AccountHistoryRepository $accountHistoryRepository)
    {
    }

    /**
     * @param int $status
     */
    public function changeAccountStatus($status, array|int $entryId): bool|int
    {
        $result = $this->accountRepository->update(['status' => $status], $entryId);

        if (\is_array($entryId)) {
            $accountId = $this->retrieveAccountId($entryId);

            $this->addAccountHistory($status, $accountId);
        } else {
            $this->addAccountHistory($status, $entryId);
        }

        return $result;
    }

    /**
     * @param int $status
     * @param int $accountId
     */
    protected function addAccountHistory($status, $accountId): bool|int
    {
        $historyInsertValues = [
            'newsletter_account_id' => $accountId,
            'date' => $this->date->toSQL(),
            'action' => $status,
        ];

        return $this->accountHistoryRepository->insert($historyInsertValues);
    }

    /**
     * @return int
     */
    protected function retrieveAccountId(array $entry)
    {
        switch (key($entry)) {
            case 'mail':
                $account = $this->accountRepository->getOneByEmail($entry['mail']);

                break;
            case 'hash':
                $account = $this->accountRepository->getOneByHash($entry['hash']);
        }

        return (!empty($account)) ? $account['id'] : 0;
    }
}
