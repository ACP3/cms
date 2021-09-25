<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Contact\Model\ContactsModel;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Contact\Model\ContactsModel
     */
    private $contactsModel;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        FormAction $actionHelper,
        ContactsModel $contactsModel
    ) {
        parent::__construct($context);

        $this->contactsModel = $contactsModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function __invoke(?string $action)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->contactsModel->delete($items);
            }
        );
    }
}
