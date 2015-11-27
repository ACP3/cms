<?php
namespace ACP3\Modules\ACP3\Newsletter\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\AccountRepository;

/**
 * Class AccountExistsValidationRule
 * @package ACP3\Modules\ACP3\Newsletter\Validator\ValidationRules
 */
class AccountExistsValidationRule extends AbstractValidationRule
{
    const NAME = 'newsletter_account_exists';

    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $accountRepository;

    /**
     * AccountExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->accountRepository->accountExists($data);
    }
}