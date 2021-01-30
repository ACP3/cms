<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Polls\Model\PollsModel
     */
    private $pollsModel;
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\AdminPollEditViewProvider
     */
    private $adminPollEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Polls\Model\PollsModel $pollsModel,
        Polls\ViewProviders\AdminPollEditViewProvider $adminPollEditViewProvider
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
        $this->adminPollEditViewProvider = $adminPollEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $poll = $this->pollsModel->getOneById($id);

        if (empty($poll) === false) {
            return ($this->adminPollEditViewProvider)($poll);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
