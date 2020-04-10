<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository
     */
    private $guestbookRepository;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Guestbook\Model\Repository\GuestbookRepository $guestbookRepository
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->guestbookRepository = $guestbookRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $guestbookSettings = $this->config->getSettings(Guestbook\Installer\Schema::MODULE_NAME);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Guestbook\Installer\Schema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->guestbookRepository->countAll($guestbookSettings['notify']));

        $guestbook = $this->guestbookRepository->getAll(
            $guestbookSettings['notify'],
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        try {
            return [
                'guestbook' => $guestbook,
                'overlay' => $guestbookSettings['overlay'],
                'pagination' => $this->pagination->render(),
                'dateformat' => $guestbookSettings['dateformat'],
            ];
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
