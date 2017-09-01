<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class FileDetailsBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * FileDetailsBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     */
    public function __construct(BlockContext $context, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $file = $this->getData();

        $this->breadcrumb
            ->append($this->translator->t('files', 'files'), 'files')
            ->append($file['category_title'], 'files/index/files/cat_' . $file['category_id'])
            ->append($file['title']);

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);
        $file['text'] = $this->view->fetchStringAsTemplate($file['text']);

        return [
            'file' => $file,
            'dateformat' => $settings['dateformat'],
            'comments_allowed' => $settings['comments'] == 1 && $file['comments'] == 1
        ];
    }
}
