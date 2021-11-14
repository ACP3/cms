<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Gallery\Model\PictureModel $pictureModel
    ) {
        parent::__construct($context);
    }

    public function __invoke(int $id, ?string $action = null): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            fn (array $items) => (bool) $this->pictureModel->delete($items),
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/pictures/index/id_' . $id
        );
    }
}
