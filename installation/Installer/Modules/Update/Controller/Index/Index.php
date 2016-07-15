<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Update\Controller\Index;

use ACP3\Core\Cache;
use ACP3\Installer\Core;
use ACP3\Installer\Modules\Update\Model\SchemaUpdateModel;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Update\Controller\Index
 */
class Index extends Core\Controller\AbstractInstallerAction
{
    /**
     * @var SchemaUpdateModel
     */
    protected $schemaUpdateModel;

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
     * @param string $action
     * @return array
     */
    public function execute($action = '')
    {
        if ($action === 'confirmed') {
            return $this->executePost();
        }
    }

    /**
     * @return array
     */
    protected function executePost()
    {
        $results = $this->schemaUpdateModel->updateModules($this->container);
        ksort($results);

        $this->setTemplate('Update/index.result.tpl');
        $this->clearCaches();

        return [
            'results' => $results
        ];
    }

    protected function clearCaches()
    {
        Cache\Purge::doPurge([
            ACP3_ROOT_DIR . 'cache/',
            $this->appPath->getUploadsDir() . 'assets'
        ]);
    }
}
