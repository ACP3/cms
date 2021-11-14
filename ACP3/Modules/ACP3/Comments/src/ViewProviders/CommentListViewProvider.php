<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Pagination;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Comments\Repository\CommentRepository;

class CommentListViewProvider
{
    public function __construct(private Modules $modules, private Pagination $pagination, private ResultsPerPage $resultsPerPage, private SettingsInterface $settings, private Translator $translator, private CommentRepository $commentRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidPageException
     */
    public function __invoke(string $moduleName, int $entryId): array
    {
        $commentsSettings = $this->settings->getSettings(CommentsSchema::MODULE_NAME);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(CommentsSchema::MODULE_NAME);
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults(
                $this->commentRepository->countAllByModule($this->modules->getModuleId($moduleName), $entryId)
            );

        $comments = $this->commentRepository->getAllByModule(
            $this->modules->getModuleId($moduleName),
            $entryId,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        foreach ($comments as $i => $comment) {
            if (empty($comment['name'])) {
                $comments[$i]['name'] = $this->translator->t('users', 'deleted_user');
            }
        }

        return [
            'comments' => $comments,
            'dateformat' => $commentsSettings['dateformat'],
            'pagination' => $this->pagination->render(),
        ];
    }
}
