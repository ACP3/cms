<?php
namespace ACP3\Modules\ACP3\Newsletter\Helper;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Newsletter\Model\AccountHistoryRepository;
use ACP3\Modules\ACP3\Newsletter\Model\AccountRepository;

/**
 * Class AccountStatus
 * @package ACP3\Modules\ACP3\Newsletter\Helper
 */
class AccountStatus
{
    const ACCOUNT_STATUS_CONFIRMATION_NEEDED = 0;
    const ACCOUNT_STATUS_CONFIRMED = 1;
    const ACCOUNT_STATUS_DISABLED = 2;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $accountRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountHistoryRepository
     */
    protected $accountHistoryRepository;

    /**
     * @param \ACP3\Core\Date                                              $date
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository        $accountRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountHistoryRepository $accountHistoryRepository
     */
    public function __construct(
        Date $date,
        AccountRepository $accountRepository,
        AccountHistoryRepository $accountHistoryRepository
    )
    {
        $this->date = $date;
        $this->accountRepository = $accountRepository;
        $this->accountHistoryRepository = $accountHistoryRepository;
    }

    /**
     * @param int       $status
     * @param int|array $id
     *
     * @return bool|int
     */
    public function changeAccountStatus($status, $id)
    {
        $bool = $this->accountRepository->update(
            ['status' => $status],
            $id
        );

        if (is_array($id)) {
            $accountId = $this->retrieveAccountId($id);

            $this->addAccountHistory($status, $accountId);
        } else {
            $this->addAccountHistory($status, $id);
        }

        return $bool;
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
            'action' => $status
        ];
        return $this->accountHistoryRepository->insert($historyInsertValues);
    }

    /**
     * @param array $id
     *
     * @return int
     */
    protected function retrieveAccountId(array $id)
    {
        switch (key($id)) {
            case 'mail':
                $account = $this->accountRepository->getOneByEmail($id['mail']);
                break;
            case 'hash':
                $account = $this->accountRepository->getOneByHash($id['hash']);
        }

        return (!empty($account)) ? $account['id'] : 0;
    }
}