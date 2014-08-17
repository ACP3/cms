<?php

namespace ACP3\Installer\Core;

use ACP3\Core;
use Doctrine\DBAL\Connection;

/**
 * Class Functions
 * @package ACP3\Installer\Core
 */
class Functions
{
    /**
     * @var Connection
     */
    protected $db;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;

    public function __construct(
        Connection $db,
        Lang $lang,
        Core\Modules $modules,
        Core\Validator\Rules\Misc $validate
    )
    {
        $this->db = $db;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->validate = $validate;
    }

    /**
     * Führt die Datenbankschema-Änderungen durch
     *
     * @param array $queries
     *    Array mit den durchzuführenden Datenbankschema-Änderungen
     * @param integer $version
     *    Version der Datenbank, auf welche aktualisiert werden soll
     * @return array
     */
    public function executeSqlQueries(array $queries, $version)
    {
        $bool = Core\Modules\AbstractInstaller::executeSqlQueries($queries);

        $result = array(
            'text' => sprintf($this->lang->t('update', 'update_db_version_to'), $version),
            'class' => $bool === true ? 'success' : 'important',
            'result_text' => $this->lang->t('update', $bool === true ? 'db_update_success' : 'db_update_error')
        );

        return $result;
    }

    /**
     * Setzt die Ressourcen-Tabelle auf die Standardwerte zurück
     */
    public function resetResources($mode = 1)
    {
        $this->db->executeUpdate('TRUNCATE TABLE ' . DB_PRE . 'acl_resources');

        // Moduldaten in die ACL schreiben
        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/Installer.php';
            if (is_file($path) === true) {
                $className = Core\Modules\AbstractInstaller::buildClassName($module);
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = new $className(Core\Registry::get('Db'));
                $installer->addResources($mode);
            }
        }
    }

}
