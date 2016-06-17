<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractAdminAction;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms                   $formsHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelpers)
    {
        parent::__construct($context);

        $this->formsHelpers = $formsHelpers;
    }


    /**
     * @param array $userRoles
     *
     * @return array
     */
    protected function fetchUserRoles(array $userRoles = [])
    {
        $roles = $this->acl->getAllRoles();
        $cRoles = count($roles);
        for ($i = 0; $i < $cRoles; ++$i) {
            $roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
            $roles[$i]['selected'] = $this->formsHelpers->selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $userRoles) ? $roles[$i]['id'] : '');
        }
        return $roles;
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
