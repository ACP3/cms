<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Core\Date;
use ACP3\Modules\ACP3\Polls\Helpers as PollHelpers;
use ACP3\Modules\ACP3\Polls\Repository\PollRepository;

class PollListViewProvider
{
    public function __construct(private readonly Date $date, private readonly PollHelpers $pollHelpers, private readonly PollRepository $pollRepository)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $polls = $this->pollRepository->getAll($this->date->getCurrentDateTime());

        foreach ($polls as $i => $poll) {
            if ($this->pollHelpers->hasAlreadyVoted($poll['id'])
                || ($poll['start'] !== $poll['end'] && $this->date->timestamp($poll['end']) <= $this->date->timestamp())
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
