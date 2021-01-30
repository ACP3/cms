<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
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

    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    public function __invoke(CustomOptionEvent $customOptionEvent)
    {
        if ($customOptionEvent->getIdentifier() === $this->getDataGridIdentifier() &&
            $this->acl->hasPermission($this->getResource()) === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                $this->getRoute($dbResultRow),
                $this->translator->t('system', 'duplicate_entry'),
                'fa-copy',
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
     * @return string
     */
    abstract protected function getRoute(array $dbResultRow);
}
