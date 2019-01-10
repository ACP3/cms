<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact\Model\ContactsModel;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Contact\Model\ContactsModel
     */
    private $contactsModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        ContactsModel $contactsModel
    ) {
        parent::__construct($context);

        $this->contactsModel = $contactsModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(?string $action)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                return $this->contactsModel->delete($items);
            }
        );
    }
}
