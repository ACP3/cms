<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext    $context
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Users\Model\UserRepository $userRepository)
    {
        parent::__construct($context);

        $this->userRepository = $userRepository;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = $isAdminUser = $selfDelete = false;
                foreach ($items as $item) {
                    if ($item == 1) {
                        $isAdminUser = true;
                    } else {
                        // Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
                        if ($item == $this->user->getUserId()) {
                            $this->user->logout();
                            $selfDelete = true;
                        }
                        $bool = $this->userRepository->delete($item);
                    }
                }
                if ($isAdminUser === true) {
                    $bool = false;
                    $text = $this->translator->t('users', 'admin_user_undeletable');
                } else {
                    $text = $this->translator->t('system', $bool !== false ? 'delete_success' : 'delete_error');
                }

                return $this->redirectMessages()->setMessage($bool, $text,
                    $selfDelete === true ? $this->appPath->getWebRoot() : '');
            }
        );
    }
}
