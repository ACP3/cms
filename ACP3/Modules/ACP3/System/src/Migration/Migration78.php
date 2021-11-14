<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Migration;

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\Exception\WrongMigrationNameException;
use ACP3\Core\Migration\MigrationInterface;
use ACP3\Core\Migration\MigrationServiceLocator;

final class Migration78 implements MigrationInterface
{
    /**
     * @var Connection
     */
    private $db;
    /**
     * @var MigrationServiceLocator
     */
    private $migrationServiceLocator;

    public function __construct(Connection $db, MigrationServiceLocator $migrationServiceLocator)
    {
        $this->db = $db;
        $this->migrationServiceLocator = $migrationServiceLocator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function up(): void
    {
        $this->markMigrationsAsExecuted();

        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}modules` DROP COLUMN `version`;");
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return null;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function markMigrationsAsExecuted(): void
    {
        $moduleNamesWithSchemaVersion = $this->db->fetchAll("SELECT `name`, `version` FROM {$this->db->getPrefix()}modules");
        $migrations = $this->migrationServiceLocator->getMigrations();

        foreach ($moduleNamesWithSchemaVersion as $result) {
            $path = ComponentRegistry::getPathByName($result['name']);

            if (!is_file($path . '/composer.json')) {
                continue;
            }

            $composerConfig = json_decode(file_get_contents($path . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
            $namespacePrefixes = array_keys($composerConfig['autoload']['psr-4']);

            $migrationsToMarkAsExecuted = array_filter($migrations, function (MigrationInterface $migration) use ($namespacePrefixes, $result) {
                foreach ($namespacePrefixes as $namespacePrefix) {
                    if ($this->getSchemaVersion($migration) <= (int) $result['version'] && strpos(\get_class($migration), (string) $namespacePrefix) === 0) {
                        return true;
                    }
                }

                return false;
            });

            foreach ($migrationsToMarkAsExecuted as $migration) {
                $this->db->executeQuery(
                    "INSERT INTO {$this->db->getPrefix()}migration (`name`) VALUES (:migration)",
                    ['migration' => \get_class($migration)]
                );
            }
        }
    }

    private function getSchemaVersion(MigrationInterface $migration): int
    {
        $className = (new \ReflectionClass($migration))->getShortName();

        if (!preg_match('/^Migration(\d+)$/', $className, $matches)) {
            throw new WrongMigrationNameException(sprintf('The migration file %s doesn\'t comply to the naming convention "Migration[0-9]+"!', __CLASS__));
        }

        return (int) $matches[1];
    }
}
