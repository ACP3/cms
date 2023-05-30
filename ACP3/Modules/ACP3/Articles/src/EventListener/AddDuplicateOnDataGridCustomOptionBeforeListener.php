<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\EventListener;

use ACP3\Core\Model\Event\Listener\AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddDuplicateOnDataGridCustomOptionBeforeListener extends AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener implements EventSubscriberInterface
{
    protected function getDataGridIdentifier(): string
    {
        return '#articles-data-grid';
    }

    protected function getResource(): string
    {
        return 'admin/articles/index/duplicate';
    }

    protected function getRoute(array $dbResultRow): string
    {
        return 'acp/articles/index/duplicate/id_' . $dbResultRow['id'];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'data_grid.column_renderer.custom_option_before' => '__invoke',
        ];
    }
}
