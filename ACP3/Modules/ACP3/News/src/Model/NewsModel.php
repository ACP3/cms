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
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    use DuplicationAwareTrait;

    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var SettingsInterface
     */
    protected $config;

    /**
     * NewsModel constructor.
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        SettingsInterface $config,
        NewsRepository $newsRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $newsRepository);

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $newsId = null)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $data = \array_merge($data, [
            'updated_at' => 'now',
            'readmore' => $this->useReadMore($data, $settings),
            'comments' => $this->useComments($data, $settings),
            'category_id' => $data['cat'] ?? $data['category_id'],
        ]);

        return parent::save($data, $newsId);
    }

    /**
     * @return int
     */
    protected function useReadMore(array $formData, array $settings)
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    /**
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'readmore' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'comments' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
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
    protected function getDefaultDataForDuplication()
    {
        return [
            'active' => 0,
            'start' => 'now',
            'end' => 'now',
        ];
    }
}
