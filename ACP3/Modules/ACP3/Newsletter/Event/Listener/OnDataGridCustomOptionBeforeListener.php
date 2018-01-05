<?php

namespace ACP3\Modules\ACP3\Newsletter\Event\Listener;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\TranslatorInterface;

class OnDataGridCustomOptionBeforeListener
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    private $translator;

    /**
     * OnDataGridCustomOptionBeforeListener constructor.
     * @param ACLInterface $acl
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ACLInterface $acl,
        TranslatorInterface $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent $customOptionEvent
     */
    public function onDataGridCustomOptionBefore(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === '#newsletter-data-grid' &&
            $this->acl->hasPermission('admin/newsletter/index/send') === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            if (array_key_exists('status', $dbResultRow) && $dbResultRow['status'] != 1) {
                $customOptionEvent->getOptionRenderer()->addOption(
                    'acp/newsletter/index/send/id_' . $dbResultRow['id'],
                    $this->translator->t('newsletter', 'send'),
                    'fa-envelope',
                    'btn-primary',
                    true
                );
            }
        }
    }
}
