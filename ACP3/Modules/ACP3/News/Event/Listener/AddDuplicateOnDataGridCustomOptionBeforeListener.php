<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;

/**
 * Class AddDuplicateOnDataGridCustomOptionBeforeListener
 * @package ACP3\Modules\ACP3\News\Event\Listener
 */
class AddDuplicateOnDataGridCustomOptionBeforeListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * OnDataGridCustomOptionBeforeListener constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent $customOptionEvent
     */
    public function addDuplicateEntryButton(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === '#news-data-grid' &&
            $this->acl->hasPermission('admin/news/index/duplicate') === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                'acp/news/index/duplicate/id_' . $dbResultRow['id'],
                $this->translator->t('system', 'duplicate_entry'),
                'glyphicon-repeat',
                'btn-default',
                true
            );
        }
    }
}
