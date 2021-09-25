<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Index extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Repository\PollRepository
     */
    private $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\PollWidgetViewProvider
     */
    private $pollWidgetViewProvider;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Helpers
     */
    private $pollHelpers;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Polls\Repository\PollRepository $pollRepository,
        Polls\ViewProviders\PollWidgetViewProvider $pollWidgetViewProvider,
        Polls\Helpers $pollHelpers
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->pollWidgetViewProvider = $pollWidgetViewProvider;
        $this->pollHelpers = $pollHelpers;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $poll = $this->pollRepository->getLatestPoll($this->date->getCurrentDateTime());

        if (!empty($poll) && $this->pollHelpers->hasAlreadyVoted($poll['id'])) {
            $this->setTemplate('Polls/Widget/index.result.tpl');
        } else {
            $this->setTemplate('Polls/Widget/index.vote.tpl');
        }

        return ($this->pollWidgetViewProvider)($poll);
    }
}
