<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnDataGridCustomOptionBeforeListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

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
                    'envelope',
                    'btn-primary',
                    true
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'data_grid.column_renderer.custom_option_before' => '__invoke',
        ];
    }
}
