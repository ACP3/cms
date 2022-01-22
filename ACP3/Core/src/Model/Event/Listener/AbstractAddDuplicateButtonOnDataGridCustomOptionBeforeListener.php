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
    public function __construct(private ACL $acl, private Translator $translator)
    {
    }

    public function __invoke(CustomOptionEvent $customOptionEvent): void
    {
        if ($customOptionEvent->getIdentifier() === $this->getDataGridIdentifier() &&
            $this->acl->hasPermission($this->getResource()) === true
        ) {
            $dbResultRow = $customOptionEvent->getDbResultRow();

            $customOptionEvent->getOptionRenderer()->addOption(
                $this->getRoute($dbResultRow),
                $this->translator->t('system', 'duplicate_entry'),
                'copy',
                'btn-outline-secondary',
                true
            );
        }
    }

    abstract protected function getDataGridIdentifier(): string;

    abstract protected function getResource(): string;

    /**
     * @param array<string, mixed> $dbResultRow
     */
    abstract protected function getRoute(array $dbResultRow): string;
}
