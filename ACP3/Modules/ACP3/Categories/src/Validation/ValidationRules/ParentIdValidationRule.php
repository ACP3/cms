<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;

class ParentIdValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoriesRepository;

    public function __construct(CategoryRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkParentIdExists($data);
    }

    /**
     * @param string $value
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function checkParentIdExists($value): bool
    {
        if (empty($value)) {
            return true;
        }

        return $this->categoriesRepository->resultExists((int) $value);
    }
}
