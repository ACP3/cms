<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class Sort extends AbstractFrontendAction
{
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    private $sortHelper;

    /**
     * Sort constructor.
     *
     * @param FrontendContext         $context
     * @param FilesRepository         $filesRepository
     * @param \ACP3\Core\Helpers\Sort $sortHelper
     */
    public function __construct(FrontendContext $context, FilesRepository $filesRepository, Core\Helpers\Sort $sortHelper)
    {
        parent::__construct($context);

        $this->filesRepository = $filesRepository;
        $this->sortHelper = $sortHelper;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->filesRepository->resultExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(FilesRepository::TABLE_NAME, 'id', 'sort', $id);
            } else {
                $this->sortHelper->down(FilesRepository::TABLE_NAME, 'id', 'sort', $id);
            }

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirect()->temporary('acp/files');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
