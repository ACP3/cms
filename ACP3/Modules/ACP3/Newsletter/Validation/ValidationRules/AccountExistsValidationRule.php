<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository;

class AccountExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository
     */
    protected $accountRepository;

    /**
     * AccountExistsValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository $accountRepository
     */
    public function __construct(NewsletterAccountsRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkAccountExists($data);
    }

    /**
     * @param string $data
     * @return bool
     */
    protected function checkAccountExists($data)
    {
        return $this->accountRepository->accountExists($data);
    }
}
