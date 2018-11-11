<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var Emoticons\Model\EmoticonsModel
     */
    protected $emoticonsModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Emoticons\Model\EmoticonsModel $emoticonsModel
    ) {
        parent::__construct($context);

        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->emoticonsModel->delete($items);
            }
        );
    }
}
