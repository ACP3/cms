<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\I18n\Translator;

abstract class AbstractAddDuplicateButtonOnDataGridCustomOptionBeforeListener
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    /**
     * OnDataGridCustomOptionBeforeListener constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent $customOptionEvent
     */
    public function addDuplicateEntryButton(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === $this->getDataGridIdentifier() &&
            $this->acl->hasPermission($this->getResource()) === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                $this->getRoute($dbResultRow),
                $this->translator->t('system', 'duplicate_entry'),
                'glyphicon-repeat',
                'btn-default',
                true
            );
        }
    }

    /**
     * @return string
     */
    abstract protected function getDataGridIdentifier();

    /**
     * @return string
     */
    abstract protected function getResource();

    /**
     * @param array $dbResultRow
     * @return string
     */
    abstract protected function getRoute(array $dbResultRow);
}
