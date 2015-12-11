<?php
namespace ACP3\Modules\ACP3\Newsletter\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;

/**
 * Class OnDataGridCustomOptionBeforeListener
 * @package ACP3\Modules\ACP3\Newsletter\Event\Listener
 */
class OnDataGridCustomOptionBeforeListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $lang;

    /**
     * OnDataGridCustomOptionBeforeListener constructor.
     *
     * @param \ACP3\Core\ACL             $acl
     * @param \ACP3\Core\I18n\Translator $lang
     */
    public function __construct(
        ACL $acl,
        Translator $lang
    )
    {
        $this->acl = $acl;
        $this->lang = $lang;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent $customOptionEvent
     */
    public function onDataGridCustomOptionBefore(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === '#newsletter-data-grid' &&
            $this->acl->hasPermission('admin/newsletter/index/send')
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            if (array_key_exists('status', $dbResultRow) && $dbResultRow['status'] != 1) {
                $customOptionEvent->getOptionRenderer()->addOption(
                    'acp/newsletter/index/send/id_' . $dbResultRow['id'],
                    $this->lang->t('newsletter', 'send'),
                    'glyphicon-envelope',
                    'btn-primary',
                    true
                );
            }
        }
    }
}