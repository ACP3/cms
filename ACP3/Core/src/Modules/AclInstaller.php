<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Repository\AbstractRepository;

class AclInstaller implements InstallerInterface
{
    public const INSTALL_RESOURCES_AND_RULES = 1;
    public const INSTALL_RESOURCES = 2;

    /**
     * @var \ACP3\Core\Modules\SchemaHelper
     */
    private $schemaHelper;
    /**
     * @var \ACP3\Core\Repository\AbstractRepository
     */
    private $resourceRepository;

    public function __construct(
        SchemaHelper $schemaHelper,
        AbstractRepository $resourceRepository
    ) {
        $this->schemaHelper = $schemaHelper;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * Fügt die zu einen Modul zugehörigen Ressourcen ein.
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function install(SchemaInterface $schema, int $mode = self::INSTALL_RESOURCES_AND_RULES)
    {
        $this->insertAclResources($schema);

        if ($mode === self::INSTALL_RESOURCES_AND_RULES) {
            $this->insertAclPermissions($schema->getModuleName());
        }

        return true;
    }

    /**
     * Inserts a new resource into the database.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertAclResources(SchemaInterface $schema): void
    {
        $moduleId = $this->schemaHelper->getModuleId($schema->getModuleName());

        foreach ($schema->specialResources() as $area => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $privilegeId) {
                    $insertValues = [
                        'module_id' => $moduleId,
                        'area' => !empty($area) ? strtolower($area) : AreaEnum::AREA_FRONTEND,
                        'controller' => strtolower($controller),
                        'page' => $this->convertCamelCaseToUnderscore($action),
                        'params' => '',
                        'privilege_id' => (int) $privilegeId,
                    ];
                    $this->resourceRepository->insert($insertValues);
                }
            }
        }
    }

    private function convertCamelCaseToUnderscore(string $action): string
    {
        return strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
    }

    /**
     * Insert new acl user rules.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertAclPermissions(string $moduleName): void
    {
        // @TODO: Insert resources
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen.
     */
    public function uninstall(SchemaInterface $schema): bool
    {
        return true;
    }
}
