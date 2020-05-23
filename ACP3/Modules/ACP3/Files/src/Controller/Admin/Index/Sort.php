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
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        FilesRepository $filesRepository,
        Core\Helpers\Sort $sortHelper)
    {
        parent::__construct($context);

        $this->filesRepository = $filesRepository;
        $this->sortHelper = $sortHelper;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if (($action === 'up' || $action === 'down') && $this->filesRepository->resultExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(FilesRepository::TABLE_NAME, 'id', 'sort', $id);
            } else {
                $this->sortHelper->down(FilesRepository::TABLE_NAME, 'id', 'sort', $id);
            }

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirectResponse->temporary('acp/files');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
