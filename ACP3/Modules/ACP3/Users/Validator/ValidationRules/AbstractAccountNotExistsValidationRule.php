<?php
namespace ACP3\Modules\ACP3\Users\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Users\Model\UserRepository;

/**
 * Class AccountExistsValidationRule
 * @package ACP3\Modules\ACP3\Users\Validator\ValidationRules
 */
abstract class AbstractAccountNotExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;

    /**
     * AccountExistsByNameValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed  $data
     * @param string $field
     * @param array  $extra
     *
     * @return boolean
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->accountExists($data, isset($extra['user_id']) ? $extra['user_id'] : 0);
    }

    /**
     * @param string $data
     * @param int    $userId
     *
     * @return bool
     */
    abstract function accountExists($data, $userId);
}