<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\ViewProviders;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema as GuestbookSchema;
use ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository;

class GuestbookListViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository
     */
    private $guestbookRepository;
    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        GuestbookRepository $guestbookRepository,
        Pagination $pagination,
        ResultsPerPage $resultsPerPage,
        SettingsInterface $settings
    ) {
        $this->guestbookRepository = $guestbookRepository;
        $this->pagination = $pagination;
        $this->resultsPerPage = $resultsPerPage;
        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $guestbookSettings = $this->settings->getSettings(GuestbookSchema::MODULE_NAME);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(GuestbookSchema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->guestbookRepository->countAll($guestbookSettings['notify']));

        $guestbook = $this->guestbookRepository->getAll(
            $guestbookSettings['notify'],
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        return [
                'guestbook' => $guestbook,
                'overlay' => $guestbookSettings['overlay'],
                'pagination' => $this->pagination->render(),
                'dateformat' => $guestbookSettings['dateformat'],
            ];
    }
}
