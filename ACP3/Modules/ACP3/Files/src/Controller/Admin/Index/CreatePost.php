<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Files\Services\FileUpsertService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private readonly FormAction $actionHelper,
        private readonly FileUpsertService $fileUpsertService,
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();
            if (isset($formData['external'])) {
                $file = $formData['file_external'];
            } else {
                $file = $this->request->getFiles()->get('file_internal');
            }

            return $this->fileUpsertService->upsert($formData, $file);
        });
    }
}
