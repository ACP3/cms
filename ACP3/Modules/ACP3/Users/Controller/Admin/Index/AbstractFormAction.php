<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\Forms                      $formsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelpers
    ) {
        parent::__construct($context);

        $this->formsHelpers = $formsHelpers;
    }

    /**
     * @return array
     */
    protected function fetchUserRoles(array $currentUserRoles = [])
    {
        $roles = $this->acl->getAllRoles();

        $availableUserRoles = [];
        foreach ($roles as $role) {
            $availableUserRoles[$role['id']] = \str_repeat('&nbsp;&nbsp;', $role['level']) . $role['name'];
        }

        return $this->formsHelpers->choicesGenerator('roles', $availableUserRoles, $currentUserRoles);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    protected function fetchIsSuperUser($value = 0)
    {
        return $this->formsHelpers->yesNoCheckboxGenerator('super_user', $value);
    }
}
