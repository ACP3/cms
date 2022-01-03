<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Date;
use ACP3\Core\Repository\FloodBarrierAwareRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractFloodBarrierValidationRule extends AbstractValidationRule
{
    public function __construct(protected Date $date, protected FloodBarrierAwareRepositoryInterface $repository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        $date = $this->repository->getLastDateFromIp($extra['ip']);
        $floodTime = !empty($date) ? $this->date->timestamp($date, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        return $floodTime <= $time;
    }
}
