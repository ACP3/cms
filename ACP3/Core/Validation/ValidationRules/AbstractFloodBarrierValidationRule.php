<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Date;
use ACP3\Core\Model\Repository\FloodBarrierAwareRepositoryInterface;

abstract class AbstractFloodBarrierValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Model\Repository\FloodBarrierAwareRepositoryInterface
     */
    protected $repository;

    /**
     * FloodBarrierValidationRule constructor.
     *
     * @param \ACP3\Core\Date                                       $date
     * @param \ACP3\Core\Model\Repository\FloodBarrierAwareRepositoryInterface $repository
     */
    public function __construct(
        Date $date,
        FloodBarrierAwareRepositoryInterface $repository
    ) {
        $this->date = $date;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $date = $this->repository->getLastDateFromIp($extra['ip']);
        $floodTime = !empty($date) ? $this->date->timestamp($date, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        return $floodTime <= $time;
    }
}
