<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountHistoryRepository;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository;

class AccountStatus
{
    public const ACCOUNT_STATUS_CONFIRMATION_NEEDED = 0;
    public const ACCOUNT_STATUS_CONFIRMED = 1;
    public const ACCOUNT_STATUS_DISABLED = 2;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository
     */
    protected $accountRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountHistoryRepository
     */
    protected $accountHistoryRepository;

    public function __construct(
        Date $date,
        AccountRepository $accountRepository,
        AccountHistoryRepository $accountHistoryRepository
    ) {
        $this->date = $date;
        $this->accountRepository = $accountRepository;
        $this->accountHistoryRepository = $accountHistoryRepository;
    }

    /**
     * @param int       $status
     * @param int|array $entryId
     *
     * @return bool|int
     */
    public function changeAccountStatus($status, $entryId)
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
     *
     * @return bool|int
     */
    protected function addAccountHistory($status, $accountId)
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
        switch (\key($entry)) {
            case 'mail':
                $account = $this->accountRepository->getOneByEmail($entry['mail']);

                break;
            case 'hash':
                $account = $this->accountRepository->getOneByHash($entry['hash']);
        }

        return (!empty($account)) ? $account['id'] : 0;
    }
}
