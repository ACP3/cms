<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Pagination                         $pagination
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Pagination $pagination,
        Users\Model\UserRepository $userRepository
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $users = $this->userRepository->getAll(POS, $this->user->getEntriesPerPage());
        $allUsers = $this->userRepository->countAll();

        $this->pagination->setTotalResults($allUsers);

        return [
            'users' => $users,
            'pagination' => $this->pagination->render(),
            'all_users' => $allUsers
        ];
    }
}