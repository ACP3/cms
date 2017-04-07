<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsletterModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var SettingsInterface
     */
    protected $config;

    /**
     * NewsletterModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param SettingsInterface $config
     * @param NewsletterRepository $newsletterRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        SettingsInterface $config,
        NewsletterRepository $newsletterRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $newsletterRepository);

        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data, $entryId = null)
    {
        if ($entryId === null) {
            $settings = $this->config->getSettings(Schema::MODULE_NAME);
            $data['html'] = $settings['html'];
            $data['status'] = 0;
        }

        $data['updated_at'] = 'now';

        return parent::save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'date' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'html' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'status' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
