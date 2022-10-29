<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\Event;

/**
 * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0. Use `BeforeModelSaveEvent` or `AfterModelSaveEvent` instead.
 */
class ModelSaveEvent extends AbstractModelSaveEvent
{
    /**
     * @deprecated since ACP3 version 6.11.0, to be removed with version 7.0.0. Use `BeforeModelDeleteEvent` or `AfterModelDeleteEvent` instead.
     * @see BeforeModelDeleteEvent
     * @see AfterModelDeleteEvent
     */
    public function isDeleteStatement(): bool
    {
        return \count($this->getData()) === 0 && \is_array($this->getEntryId());
    }
}
