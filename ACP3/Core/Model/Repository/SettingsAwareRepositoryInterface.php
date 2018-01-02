<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

interface SettingsAwareRepositoryInterface extends WriterRepositoryInterface
{
    /**
     * @return array
     */
    public function getAllSettings();
}
