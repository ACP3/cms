<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Pagination                         $pagination
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository $userRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Users\Model\Repository\UserRepository $userRepository
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
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Users\Installer\Schema::MODULE_NAME);
        $allUsers = $this->userRepository->countAll();
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($allUsers);

        $users = $this->userRepository->getAll(
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        return [
            'users' => $users,
            'pagination' => $this->pagination->render(),
            'all_users' => $allUsers,
        ];
    }
}
