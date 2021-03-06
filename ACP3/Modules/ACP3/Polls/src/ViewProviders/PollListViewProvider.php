<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Polls\Helpers as PollHelpers;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository;

class PollListViewProvider
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Helpers
     */
    private $pollHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    private $pollRepository;

    public function __construct(
        Date $date,
        PollHelpers $pollHelpers,
        PollRepository $pollRepository
    ) {
        $this->date = $date;
        $this->pollHelpers = $pollHelpers;
        $this->pollRepository = $pollRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $polls = $this->pollRepository->getAll($this->date->getCurrentDateTime());

        foreach ($polls as $i => $poll) {
            if ($this->pollHelpers->hasAlreadyVoted($poll['id']) ||
                ($poll['start'] !== $poll['end'] && $this->date->timestamp($poll['end']) <= $this->date->timestamp())
            ) {
                $polls[$i]['link'] = 'result';
            } else {
                $polls[$i]['link'] = 'vote';
            }
        }

        return [
            'polls' => $polls,
        ];
    }
}
