<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Model\Event\Listener\AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener;

class AddDuplicateOnDataGridCustomOptionBeforeListener extends AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener
{
    /**
     * @inheritdoc
     */
    protected function getDataGridIdentifier()
    {
        return '#news-data-grid';
    }

    /**
     * @inheritdoc
     */
    protected function getResource()
    {
        return 'admin/news/index/duplicate';
    }

    /**
     * @inheritdoc
     */
    protected function getRoute(array $dbResultRow)
    {
        return 'acp/news/index/duplicate/id_' . $dbResultRow['id'];
    }
}
