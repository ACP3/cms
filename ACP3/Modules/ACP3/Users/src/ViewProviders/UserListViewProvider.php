<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Modules\ACP3\Users\Installer\Schema as UsersSchema;
use ACP3\Modules\ACP3\Users\Repository\UserRepository;

class UserListViewProvider
{
    public function __construct(private readonly Pagination $pagination, private readonly ResultsPerPage $resultsPerPage, private readonly UserRepository $userRepository)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
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
