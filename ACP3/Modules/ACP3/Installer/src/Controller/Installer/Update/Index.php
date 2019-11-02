<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Update;

use ACP3\Core\Cache;
use ACP3\Modules\ACP3\Installer\Core\Controller\AbstractInstallerAction;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Model\SchemaUpdateModel;

class Index extends AbstractInstallerAction
{
    /**
     * @var \ACP3\Modules\ACP3\Installer\Model\SchemaUpdateModel
     */
    protected $schemaUpdateModel;

    public function __construct(
        InstallerContext $context,
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
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function execute(?string $action = null): ?array
    {
        if ($action === 'confirmed') {
            return $this->executePost();
        }

        return null;
    }

    /**
     * @return array
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Exception
     */
    protected function executePost(): array
    {
        $this->schemaUpdateModel->updateContainer($this->request);
        $results = $this->schemaUpdateModel->updateModules();

        $this->setTemplate('Installer/Installer/update.index.result.tpl');
        $this->clearCaches();

        return [
            'results' => $results,
        ];
    }

    protected function clearCaches(): void
    {
        Cache\Purge::doPurge([
            ACP3_ROOT_DIR . '/cache/',
            $this->appPath->getUploadsDir() . 'assets',
        ]);
    }
}
