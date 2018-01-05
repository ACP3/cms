<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository;

abstract class AbstractAccountNotExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository
     */
    protected $userRepository;

    /**
     * AccountExistsByNameValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository $userRepository
     */
    public function __construct(UsersRepository $userRepository)
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
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->accountExists($data, $extra['user_id'] ?? 0);
    }

    /**
     * @param string $data
     * @param int    $userId
     *
     * @return bool
     */
    abstract protected function accountExists($data, $userId);
}
