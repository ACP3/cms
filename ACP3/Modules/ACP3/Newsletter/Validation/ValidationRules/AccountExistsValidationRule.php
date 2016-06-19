<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository;

/**
 * Class AccountExistsValidationRule
 * @package ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules
 */
class AccountExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * AccountExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository $accountRepository
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
