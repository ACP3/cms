<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Vote extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    private $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\PollVoteViewProvider
     */
    private $pollVoteViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\ViewProviders\PollVoteViewProvider $pollVoteViewProvider
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->pollVoteViewProvider = $pollVoteViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        if ($this->pollRepository->pollExists($id, $this->date->getCurrentDateTime()) === true) {
            return ($this->pollVoteViewProvider)($id);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
