<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Update;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Migration\Migrator;
use ACP3\Modules\ACP3\Installer\Core\Controller\AbstractInstallerAction;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;

class Index extends AbstractInstallerAction
{
    /**
     * @var Migrator
     */
    private $migrator;

    public function __construct(
        InstallerContext $context,
        Migrator $migrator
    ) {
        parent::__construct($context);

        $this->migrator = $migrator;
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
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Exception
     */
    private function executePost(): array
    {
        $results = $this->migrator->updateModules();

        $this->setTemplate('Installer/Installer/update.index.result.tpl');
        $this->clearCaches();

        return [
            'results' => $results,
            'hasErrors' => $this->checkExecutedMigrationsForErrors($results),
        ];
    }

    private function clearCaches(): void
    {
        Purge::doPurge([
            ACP3_ROOT_DIR . '/cache/',
            $this->appPath->getUploadsDir() . 'assets',
        ]);
    }

    /**
     * @param array<string, \Throwable[]|null> $executedMigrations
     */
    private function checkExecutedMigrationsForErrors(array $executedMigrations): bool
    {
        foreach ($executedMigrations as $result) {
            if ($result !== null) {
                return true;
            }
        }

        return false;
    }
}
