<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Core\Model\Event\Listener\AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener;

class AddDuplicateOnDataGridCustomOptionBeforeListener extends AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener
{
    /**
     * @inheritdoc
     */
    protected function getDataGridIdentifier()
    {
        return '#files-data-grid';
    }

    /**
     * @inheritdoc
     */
    protected function getResource()
    {
        return 'admin/files/index/duplicate';
    }

    /**
     * @inheritdoc
     */
    protected function getRoute(array $dbResultRow)
    {
        return 'acp/files/index/duplicate/id_' . $dbResultRow['id'];
    }
}
