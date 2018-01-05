<?php

namespace ACP3\Modules\ACP3\Filemanager\Installer;

class Migration implements \ACP3\Core\Installer\MigrationInterface
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
