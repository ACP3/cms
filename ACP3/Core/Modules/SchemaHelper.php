<?php
namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class SchemaHelper
 * @package ACP3\Core\Modules
 */
class SchemaHelper extends ContainerAware
{
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\SettingsRepository
     */
    protected $systemSettingsRepository;

    /**
     * @param \ACP3\Core\DB                                      $db
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository   $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Model\SettingsRepository $systemSettingsRepository
     */
    public function __construct(
        Core\DB $db,
        System\Model\ModuleRepository $systemModuleRepository,
        System\Model\SettingsRepository $systemSettingsRepository
    ) {
        $this->db = $db;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemSettingsRepository = $systemSettingsRepository;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \ACP3\Core\DB
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    public function getSystemModuleRepository()
    {
        return $this->systemModuleRepository;
    }

    /**
     * Executes all given SQL queries
     *
     * @param array  $queries
     * @param string $moduleName
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executeSqlQueries(array $queries, $moduleName = '')
    {
        if (count($queries) > 0) {
            $search = ['{pre}', '{engine}', '{charset}'];
            $replace = [$this->db->getPrefix(), 'ENGINE=InnoDB', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`'];

            $this->db->getConnection()->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (is_object($query) && ($query instanceof \Closure)) {
                        if ($query() === false) {
                            return false;
                        }
                    } elseif (!empty($query)) {
                        if (strpos($query, '{moduleId}') !== false) {
                            $query = str_replace('{moduleId}', $this->getModuleId($moduleName), $query);
                        }
                        $this->db->getConnection()->query(str_ireplace($search, $replace, $query));
                    }
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollBack();

                $this->container->get('core.logger')->warning('installer', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the module-ID
     *
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId($moduleName)
    {
        return $this->systemModuleRepository->getModuleId($moduleName) ?: 0;
    }

    /**
     * @param string $moduleName
     *
     * @return boolean
     */
    public function moduleIsInstalled($moduleName)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->db->getPrefixedTableName(System\Model\ModuleRepository::TABLE_NAME)} WHERE `name` = ?", [$moduleName]) == 1;
    }
}
