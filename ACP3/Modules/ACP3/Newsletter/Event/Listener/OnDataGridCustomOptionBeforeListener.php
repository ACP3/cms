<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;

class OnDataGridCustomOptionBeforeListener
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
     */
    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    public function __invoke(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === '#newsletter-data-grid' &&
            $this->acl->hasPermission('admin/newsletter/index/send') === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            if (\array_key_exists('status', $dbResultRow) && $dbResultRow['status'] != 1) {
                $customOptionEvent->getOptionRenderer()->addOption(
                    'acp/newsletter/index/send/id_' . $dbResultRow['id'],
                    $this->translator->t('newsletter', 'send'),
                    'glyphicon-envelope',
                    'btn-primary',
                    true
                );
            }
        }
    }
}
