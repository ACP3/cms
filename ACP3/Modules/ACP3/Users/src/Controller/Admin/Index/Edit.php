<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class Edit extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AdminUserEditViewProvider
     */
    private $adminUserEditViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        UserModelInterface $user,
        Users\ViewProviders\AdminUserEditViewProvider $adminUserEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminUserEditViewProvider = $adminUserEditViewProvider;
        $this->user = $user;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function __invoke(int $id): array
    {
        $user = $this->user->getUserInfo($id);

        if (!empty($user)) {
            return ($this->adminUserEditViewProvider)($user);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
