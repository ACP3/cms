<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Files\Model\FilesModel;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class Sort extends AbstractFrontendAction
{
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\FilesModel
     */
    private $filesModel;

    public function __construct(
        FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        FilesRepository $filesRepository,
        FilesModel $filesModel
    ) {
        parent::__construct($context);

        $this->filesRepository = $filesRepository;
        $this->redirectResponse = $redirectResponse;
        $this->filesModel = $filesModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->filesRepository->resultExists($id) === true) {
            if ($action === 'up') {
                $this->filesModel->moveUp($id);
            } else {
                $this->filesModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/files');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
