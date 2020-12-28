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
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PictureModel extends AbstractModel implements SortingAwareInterface
{
    use SortingAwareTrait;

    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var PictureRepository
     */
    protected $repository;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Helpers\Sort
     */
    private $sortHelper;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        SettingsInterface $config,
        PictureRepository $pictureRepository,
        Sort $sortHelper
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $pictureRepository);

        $this->config = $config;
        $this->sortHelper = $sortHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $entryId = null)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        if ($entryId === null) {
            $data['pic'] = $this->getPictureSortIndex($data['gallery_id']);
        } else {
            $picture = $this->repository->getOneById($entryId);

            if ((int) $data['gallery_id'] !== (int) $picture['gallery_id']) {
                $data['pic'] = $this->getPictureSortIndex($data['gallery_id']);
            }
        }

        return parent::save($data, $entryId);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getPictureSortIndex(int $galleryId): int
    {
        $picNum = $this->repository->getLastPictureByGalleryId($galleryId);

        return $picNum !== null ? $picNum + 1 : 1;
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
}
