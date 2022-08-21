<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions\Services\ResourceUpsertService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class CreatePost extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private readonly FormAction $actionHelper,
        private readonly ResourceUpsertService $resourceUpsertService
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|Response
    {
        return $this->actionHelper->handleSaveAction(fn () => $this->resourceUpsertService->upsert($this->request->getPost()->all()));
    }
}
