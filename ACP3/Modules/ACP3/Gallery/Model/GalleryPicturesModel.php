<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GalleryPicturesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var GalleryPicturesRepository
     */
    protected $repository;
    /**
     * @var SettingsInterface
     */
    protected $config;

    /**
     * PictureModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param SettingsInterface $config
     * @param GalleryPicturesRepository $pictureRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        SettingsInterface $config,
        GalleryPicturesRepository $pictureRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $pictureRepository);

        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data, $entryId = null)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $data = \array_merge($data, [
            'comments' => $settings['comments'] == 1
                ? (isset($data['comments']) && $data['comments'] == 1 ? 1 : 0)
                : $settings['comments'],
        ]);

        if ($entryId === null) {
            $picNum = $this->repository->getLastPictureByGalleryId($data['gallery_id']);
            $data['pic'] = !\is_null($picNum) ? $picNum + 1 : 1;
        }

        return parent::save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'gallery_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'comments' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'file' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'pic' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
