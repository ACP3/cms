<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnDataGridCustomOptionBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly Translator $translator)
    {
    }

    public function __invoke(CustomOptionEvent $customOptionEvent): void
    {
        if ($customOptionEvent->getIdentifier() === '#gallery-data-grid'
            && $this->acl->hasPermission('admin/gallery/pictures/index') === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                'acp/gallery/pictures/index/id_' . $dbResultRow['id'],
                $this->translator->t('gallery', 'admin_pictures_index'),
                'images',
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'data_grid.column_renderer.custom_option_before' => '__invoke',
        ];
    }
}
