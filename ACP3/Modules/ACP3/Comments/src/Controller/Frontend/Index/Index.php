<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons\Helpers;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    private $commentRepository;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers|null
     */
    private $emoticonsHelpers;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Comments\Model\Repository\CommentRepository $commentRepository,
        ?Helpers $emoticonsHelpers = null
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->commentRepository = $commentRepository;
        $this->emoticonsHelpers = $emoticonsHelpers;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $module, int $entryId): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $commentsSettings = $this->config->getSettings(Comments\Installer\Schema::MODULE_NAME);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Comments\Installer\Schema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults(
                $this->commentRepository->countAllByModule($this->modules->getModuleId($module), $entryId)
            );

        $comments = $this->commentRepository->getAllByModule(
            $this->modules->getModuleId($module),
            $entryId,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        foreach ($comments as $i => $comment) {
            if (empty($comment['name'])) {
                $comments[$i]['name'] = $this->translator->t('users', 'deleted_user');
            }
            if ($this->emoticonsHelpers) {
                $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comment['message']);
            }
        }

        try {
            return [
                'comments' => $comments,
                'dateformat' => $commentsSettings['dateformat'],
                'pagination' => $this->pagination->render(),
            ];
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
