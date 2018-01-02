<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Update\Controller\Index;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Installer\Model\SchemaUpdateModel;
use ACP3\Installer\Core;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller\Index
 */
class Index extends Core\Controller\AbstractInstallerAction
{
    /**
     * @var SchemaUpdateModel
     */
    private $schemaUpdateModel;

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param SchemaUpdateModel $schemaUpdateModel
     */
    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        SchemaUpdateModel $schemaUpdateModel
    ) {
        parent::__construct($context);

        $this->schemaUpdateModel = $schemaUpdateModel;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [];
    }

    /**
     * @return array
     */
    public function executePost()
    {
        $results = $this->schemaUpdateModel->updateModules();
        ksort($results);

        $this->view->setTemplate('Update/Install/index.result.tpl');
        $this->clearCaches();

        return [
            'results' => $results
        ];
    }

    private function clearCaches(): bool
    {
        return Purge::doPurge([
            ACP3_ROOT_DIR . 'cache/',
            $this->appPath->getUploadsDir() . 'assets'
        ]);
    }
}
