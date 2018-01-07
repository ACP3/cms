<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;

class OnDataGridCustomOptionBeforeListener
{
    /**
     * @var ACL\ACLInterface
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;

    /**
     * OnDataGridCustomOptionBeforeListener constructor.
     *
     * @param ACL\ACLInterface $acl
     * @param Translator       $translator
     */
    public function __construct(
        ACL\ACLInterface $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent $customOptionEvent
     */
    public function addPicturesIndexButton(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === '#gallery-data-grid' &&
            $this->acl->hasPermission('admin/gallery/pictures/index') === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                'acp/gallery/pictures/index/id_' . $dbResultRow['id'],
                $this->translator->t('gallery', 'admin_pictures_index'),
                'fa-image',
                'btn-default'
            );
        }
    }
}
