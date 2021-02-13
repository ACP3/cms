<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core\Helpers\Sort;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\SortingAwareInterface;
use ACP3\Core\Model\SortingAwareTrait;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property PictureRepository $repository
 */
class PictureModel extends AbstractModel implements SortingAwareInterface
{
    use SortingAwareTrait;

    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    private $sortHelper;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        PictureRepository $pictureRepository,
        Sort $sortHelper
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $pictureRepository);

        $this->sortHelper = $sortHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, $entryId = null)
    {
        if ($entryId === null) {
            $rawData[$this->getSortingField()] = $this->getPictureSortIndex($rawData['gallery_id']);
        } else {
            $picture = $this->repository->getOneById($entryId);

            if ((int) $rawData['gallery_id'] !== (int) $picture['gallery_id']) {
                $rawData[$this->getSortingField()] = $this->getPictureSortIndex($rawData['gallery_id']);
            }
        }

        return parent::save($rawData, $entryId);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getPictureSortIndex(int $galleryId): int
    {
        $picNum = $this->repository->getLastPictureByGalleryId($galleryId);

        return $picNum + 1;
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'gallery_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'file' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'pic' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }

    protected function getSortHelper(): Sort
    {
        return $this->sortHelper;
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
        return 'pic';
    }

    /**
     * {@inheritDoc}
     */
    protected function getSortingConstraint(): string
    {
        return 'gallery_id';
    }
}
