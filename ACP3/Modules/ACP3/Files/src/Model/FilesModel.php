<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Model;

use ACP3\Core\Helpers\Sort;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\SortingAwareTrait;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilesModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    use DuplicationAwareTrait;
    use SortingAwareTrait;

    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var FilesRepository
     */
    protected $repository;
    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    private $sort;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository,
        Sort $sort)
    {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->sort = $sort;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $entryId = null)
    {
        $data = \array_merge($data, [
            'category_id' => $data['cat'] ?? $data['category_id'],
            'updated_at' => 'now',
        ]);

        if (!empty($data['filesize'])) {
            $data['size'] = $data['filesize'];
        }

        if ($entryId === null) {
            $data['sort'] = $this->repository->getMaxSort() + 1;
        }

        return parent::save($data, $entryId);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedColumns()
    {
        return [
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'category_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'file' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'size' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'sort' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
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

    protected function getSortHelper(): Sort
    {
        return $this->sort;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPrimaryKeyField(): string
    {
        return 'id';
    }

    /**
     * {@inheritDoc}
     */
    protected function getSortingField(): string
    {
        return 'sort';
    }
}
