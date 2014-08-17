<?php

namespace ACP3\Installer\Modules\Update;

use ACP3\Core;
use ACP3\Installer\Core\Lang;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Helpers
 * @package ACP3\Installer\Modules\Update
 */
class Helpers
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Installer\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers
     */
    protected $installHelper;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Lang $lang,
        Core\Modules $modules,
        \ACP3\Installer\Modules\Install\Helpers $installHelper
    )
    {
        $this->db = $db;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->installHelper = $installHelper;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string                                           $module
     * @param \Symfony\Component\DependencyInjection\Container $container
     *
     * @return integer
     */
    public function updateModule($module, Container $container)
    {
        $result = false;

        $serviceId = $module . '.installer';
        if ($container->has($serviceId) === true) {
            /** @var Core\Modules\AbstractInstaller $installer */
            $installer = $container->get($serviceId);
            if ($installer instanceof Core\Modules\AbstractInstaller &&
                ($this->modules->isInstalled($module) || count($installer->renameModule()) > 0)
            ) {
                $result = $installer->updateSchema();
            }
        }

        return $result;
    }

    /**
     * Setzt die Ressourcen-Tabelle auf die Standardwerte zurück
     *
     * @param int                                              $mode
     * @param \Symfony\Component\DependencyInjection\Container $container
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetResources($mode, Container $container)
    {
        $this->db->executeUpdate('TRUNCATE TABLE ' . DB_PRE . 'acl_resources');

        // Moduldaten in die ACL schreiben
        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $serviceId = $module . '.installer';
            if ($container->has($serviceId) === true) {
                $installer = $container->get($serviceId);
                $installer->addResources($mode);
            }
        }
    }

    /**
     * Führt die Datenbankschema-Änderungen durch
     *
     * @param array   $queries
     *    Array mit den durchzuführenden Datenbankschema-Änderungen
     * @param integer $version
     *    Version der Datenbank, auf welche aktualisiert werden soll
     *
     * @return array
     */
    public function executeSqlQueries(array $queries, $version)
    {
        $bool = $this->installHelper->executeSqlQueries($queries, $this->db);

        $result = array(
            'text' => sprintf($this->lang->t('update', 'update_db_version_to'), $version),
            'class' => $bool === true ? 'success' : 'important',
            'result_text' => $this->lang->t('update', $bool === true ? 'db_update_success' : 'db_update_error')
        );

        return $result;
    }
}