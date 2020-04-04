<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\Model\Event\Listener\AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener;

class AddDuplicateOnDataGridCustomOptionBeforeListener extends AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener
{
    /**
     * {@inheritdoc}
     */
    protected function getDataGridIdentifier()
    {
        return '#articles-data-grid';
    }

    /**
     * {@inheritdoc}
     */
    protected function getResource()
    {
        return 'admin/articles/index/duplicate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRoute(array $dbResultRow)
    {
        return 'acp/articles/index/duplicate/id_' . $dbResultRow['id'];
    }
}