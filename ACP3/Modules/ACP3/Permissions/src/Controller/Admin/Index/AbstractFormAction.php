<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Permissions;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository
     */
    private $privilegeRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache
     */
    private $permissionsCache;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\ACL $acl,
        Core\Modules $modules,
        Core\Helpers\Forms $formsHelper,
        Permissions\Model\Repository\PrivilegeRepository $privilegeRepository,
        Permissions\Cache $permissionsCache
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->privilegeRepository = $privilegeRepository;
        $this->permissionsCache = $permissionsCache;
        $this->acl = $acl;
        $this->modules = $modules;
    }
}
