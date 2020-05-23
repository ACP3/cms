<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Modules\ACP3\Users\Installer\Schema as UsersSchema;
use ACP3\Modules\ACP3\Users\Model\Repository\UserRepository;

class UserListViewProvider
{
    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
     */
    private $userRepository;

    public function __construct(Pagination $pagination, ResultsPerPage $resultsPerPage, UserRepository $userRepository)
    {
        $this->pagination = $pagination;
        $this->resultsPerPage = $resultsPerPage;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(UsersSchema::MODULE_NAME);
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
