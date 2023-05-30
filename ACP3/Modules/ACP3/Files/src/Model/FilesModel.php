<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Model;

use ACP3\Core\Helpers\Sort;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\DateTimeColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextWysiwygColumnType;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Core\Model\SortingAwareInterface;
use ACP3\Core\Model\SortingAwareTrait;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property FilesRepository $repository
 */
class FilesModel extends AbstractModel implements UpdatedAtAwareModelInterface, SortingAwareInterface
{
    use DuplicationAwareTrait;
    use SortingAwareTrait;

    public const EVENT_PREFIX = Schema::MODULE_NAME;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        FilesRepository $repository,
        private readonly Sort $sort)
    {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);
    }

    public function save(array $rawData, int $entryId = null): int
    {
        $rawData = array_merge($rawData, [
            'category_id' => $rawData['cat'] ?? $rawData['category_id'],
            'updated_at' => 'now',
        ]);

        if (!empty($rawData['filesize'])) {
            $rawData['size'] = $rawData['filesize'];
        }

        if ($entryId === null) {
            $rawData['sort'] = $this->repository->getMaxSort() + 1;
        }

        return parent::save($rawData, $entryId);
    }

    protected function getAllowedColumns(): array
    {
        return [
            'active' => BooleanColumnType::class,
            'start' => DateTimeColumnType::class,
            'end' => DateTimeColumnType::class,
            'updated_at' => DateTimeColumnType::class,
            'category_id' => IntegerColumnType::class,
            'title' => TextColumnType::class,
            'subtitle' => TextColumnType::class,
            'text' => TextWysiwygColumnType::class,
            'user_id' => IntegerColumnType::class,
            'file' => RawColumnType::class,
            'size' => RawColumnType::class,
            'sort' => IntegerColumnType::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultDataForDuplication(): array
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

    protected function getPrimaryKeyField(): string
    {
        return 'id';
    }

    protected function getSortingField(): string
    {
        return 'sort';
    }
}
