<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Comments\Installer\Schema;
use ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository;
use ACP3\Modules\ACP3\Emoticons\Helpers;

class CommentsListingBlock extends AbstractListingBlock
{
    /**
     * @var CommentsRepository
     */
    private $commentRepository;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Helpers
     */
    private $emoticonsHelpers;

    /**
     * CommentsListingBlock constructor.
     * @param ListingBlockContext $context
     * @param SettingsInterface $settings
     * @param CommentsRepository $commentRepository
     */
    public function __construct(
        ListingBlockContext $context,
        SettingsInterface $settings,
        CommentsRepository $commentRepository
    ) {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->settings = $settings;
    }

    /**
     * @param Helpers $emoticonsHelpers
     * @return $this
     */
    public function setEmoticonsHelper(Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
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
        $data = $this->getData();

        return $this->commentRepository->countAll($data['moduleId']);
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        $data = $this->getData();

        $comments = $this->commentRepository->getAllByModule(
            $data['moduleId'],
            $data['resultId'],
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
        $cComments = \count($comments);

        $settings = $this->settings->getSettings($this->getModuleName());

        for ($i = 0; $i < $cComments; ++$i) {
            if (empty($comments[$i]['name'])) {
                $comments[$i]['name'] = $this->translator->t('users', 'deleted_user');
            }
            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comments[$i]['message']);
            }
        }

        return $comments;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        return [
            'comments' => $this->getResults($resultsPerPage),
            'dateformat' => $this->settings->getSettings($this->getModuleName())['dateformat'],
            'pagination' => $this->pagination->render(),
        ];
    }
}
