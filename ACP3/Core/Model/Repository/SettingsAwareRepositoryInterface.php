<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

interface SettingsAwareRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array
     */
    public function getAllSettings();
}
