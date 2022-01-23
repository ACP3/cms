<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Repository\NewsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    use DuplicationAwareTrait;

    public const EVENT_PREFIX = Schema::MODULE_NAME;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        protected SettingsInterface $config,
        NewsRepository $newsRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $newsRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $rawData = array_merge($rawData, [
            'updated_at' => 'now',
            'readmore' => $this->useReadMore($rawData, $settings),
            'category_id' => $rawData['cat'] ?? $rawData['category_id'],
        ]);

        return parent::save($rawData, $entryId);
    }

    protected function useReadMore(array $formData, array $settings): int
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    protected function getAllowedColumns(): array
    {
        return [
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'readmore' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'category_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'target' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'link_title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultDataForDuplication(): array
    {
        return [
            'active' => 0,
            'start' => 'now',
            'end' => 'now',
        ];
    }
}
