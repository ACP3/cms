<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Gallery;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureModel
     */
    private $pictureModel;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Gallery\Model\PictureModel $pictureModel
    ) {
        parent::__construct($context);

        $this->actionHelper = $actionHelper;
        $this->pictureModel = $pictureModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(int $id, ?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return (bool) $this->pictureModel->delete($items);
            },
            'acp/gallery/pictures/delete/id_' . $id,
            'acp/gallery/pictures/index/id_' . $id
        );
    }
}
