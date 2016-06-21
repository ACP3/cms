<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    protected $authenticationModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Users\Model\AuthenticationModel $authenticationModel
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Users\Model\AuthenticationModel $authenticationModel,
        Users\Model\Repository\UserRepository $userRepository)
    {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
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
                            $this->authenticationModel->logout();
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

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $text,
                    $selfDelete === true ? $this->appPath->getWebRoot() : ''
                );
            }
        );
    }
}
