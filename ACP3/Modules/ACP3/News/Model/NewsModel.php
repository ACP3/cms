<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var SettingsInterface
     */
    protected $config;

    /**
     * NewsModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param SettingsInterface $config
     * @param NewsRepository $newsRepository
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
     * @inheritdoc
     */
    public function save(array $data, $newsId = null)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $data = array_merge($data, [
            'updated_at' => 'now',
            'readmore' => $this->useReadMore($data, $settings),
            'comments' => $this->useComments($data, $settings),
            'category_id' => $data['cat'],
        ]);

        return parent::save($data, $newsId);
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useReadMore(array $formData, array $settings)
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    /**
     * @param array $formData
     * @param array $settings
     *
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
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
