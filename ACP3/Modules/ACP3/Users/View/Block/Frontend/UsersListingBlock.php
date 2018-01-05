<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Users\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository;

class UsersListingBlock extends AbstractListingBlock
{
    /**
     * @var UsersRepository
     */
    private $userRepository;

    /**
     * UsersListingBlock constructor.
     * @param ListingBlockContext $context
     * @param UsersRepository $userRepository
     */
    public function __construct(ListingBlockContext $context, UsersRepository $userRepository)
    {
        parent::__construct($context);

        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalResults(): int
    {
        return $this->userRepository->countAll();
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        return $this->userRepository->getAll(
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'users' => $this->getResults($resultsPerPage),
            'pagination' => $this->pagination->render(),
            'all_users' => $this->getTotalResults(),
        ];
    }
}
