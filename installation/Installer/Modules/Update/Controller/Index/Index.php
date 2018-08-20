<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Update\Controller\Index;

use ACP3\Core\Cache;
use ACP3\Installer\Core;
use ACP3\Installer\Modules\Update\Model\SchemaUpdateModel;

class Index extends Core\Controller\AbstractInstallerAction
{
    /**
     * @var SchemaUpdateModel
     */
    protected $schemaUpdateModel;

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param SchemaUpdateModel                                        $schemaUpdateModel
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
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function execute($action = '')
    {
        if ($action === 'confirmed') {
            return $this->executePost();
        }
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function executePost()
    {
        $results = $this->schemaUpdateModel->updateModules();
        \ksort($results);

        $this->setTemplate('Update/index.result.tpl');
        $this->clearCaches();

        return [
            'results' => $results,
        ];
    }

    protected function clearCaches()
    {
        Cache\Purge::doPurge([
            ACP3_ROOT_DIR . 'cache/',
            $this->appPath->getUploadsDir() . 'assets',
        ]);
    }
}
