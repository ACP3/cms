<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticlesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * ArticlesModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        ArticleRepository $articleRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $articleRepository);
    }

    /**
     * @param array $data
     * @param int $userId
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveArticle(array $data, $userId, $entryId = null)
    {
        $data['user_id'] = $userId;

        return $this->save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
