<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Guestbook\Model\Repository\GuestbookRepository;

class GuestbookListingBlock extends AbstractListingBlock
{
    /**
     * @var GuestbookRepository
     */
    private $guestbookRepository;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Emoticons\Helpers
     */
    private $emoticonsHelpers;

    /**
     * GuestbookListingBlock constructor.
     * @param ListingBlockContext $context
     * @param SettingsInterface $settings
     * @param GuestbookRepository $guestbookRepository
     */
    public function __construct(
        ListingBlockContext $context,
        SettingsInterface $settings,
        GuestbookRepository $guestbookRepository
    ) {
        parent::__construct($context);

        $this->guestbookRepository = $guestbookRepository;
        $this->settings = $settings;
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
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
        $settings = $this->settings->getSettings($this->getModuleName());

        return $this->guestbookRepository->countAll($settings['notify']);
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        $settings = $this->settings->getSettings($this->getModuleName());

        $results = $this->guestbookRepository->getAll(
            $settings['notify'],
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
        $cResults = \count($results);

        for ($i = 0; $i < $cResults; ++$i) {
            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                $results[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($results[$i]['message']);
            }
        }

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resultsPerPage = $this->getResultsPerPage();
        $this->configurePagination($resultsPerPage);

        $settings = $this->settings->getSettings($this->getModuleName());

        return [
            'guestbook' => $this->getResults($resultsPerPage),
            'overlay' => $settings['overlay'],
            'pagination' => $this->pagination->render(),
            'dateformat' => $settings['dateformat'],
        ];
    }
}
