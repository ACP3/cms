<?php

abstract class ACP3_ModuleInstaller {

	/**
	 * Die bei der Installation an das Modul zugewiesene ID
	 *
	 * @var integer
	 */
	protected $module_id = null;

	/**
	 * Ressourcen, welche vom standardmäßigen Namensschema abweichen
	 * oder spezielle Berechtigungen benötigen
	 *
	 * @var array
	 */
	protected $special_resources = array();

	public function setModuleId($module_id)
	{
		$this->module_id = (int) $module_id;
	}
	/**
	 * Methode zum Installieren des Moduls
	 *
	 * @return boolean
	 */
	public function install() {
		$bool1 = $this->createTables();
		$bool2 = $this->addToModulesTable();
		$bool3 = $this->addSettings();
		$bool4 = $this->addResources();

		return $bool1 && $bool2 && $bool3 && $bool4;
	}

	/**
	 * Methode zum Deinstallieren des Moduls
	 *
	 * @return boolean
	 */
	public function uninstall() {
		$bool1 = $this->removeTables();
		$bool2 = $this->removeFromModulesTable();
		$bool3 = $this->removeSettings();
		$bool4 = $this->removeResources();

		return $bool1 && $bool2 && $bool3 && $bool4;
	}

	/**
	 * Fügt die zu einen Modul zugehörigen Ressourcen ein
	 *
	 * @return boolean
	 */
	public function addResources() {
		global $db;

		$mod_name = $db->select('name', 'modules', 'id = ' . $this->module_id);

		if (!empty($mod_name)) {
			$modules = scandir(MODULES_DIR . $mod_name[0]['name']);
			foreach ($modules as $row) {
				if ($row !== '.' && $row !== '..' && $row !== 'install.class.php') {
					// Erweiterungen
					$path = MODULES_DIR . $mod_name[0]['name'] . '/';
					if (is_dir($path . $row) === true && $row === 'extensions') {
						if (is_file($path . 'extensions/search.php') === true)
							$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
						if (is_file($path . 'extensions/feeds.php') === true)
							$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));
					// Normale Moduldateien
					} elseif (strpos($row, '.php') !== false) {
						// .php entfernen
						$row = substr($row, 0, -4);
						if (isset($this->special_resources[$row])) {
							$privilege_id = $this->special_resources[$row];
						} else {
							$privilege_id = 1;

							if (strpos($row, 'create') === 0)
								$privilege_id = 2;
							if (strpos($row, 'acp_') === 0)
								$privilege_id = 3;
							if (strpos($row, 'acp_create') === 0 || strpos($row, 'acp_order') === 0)
								$privilege_id = 4;
							elseif (strpos($row, 'acp_edit') === 0)
								$privilege_id = 5;
							elseif (strpos($row, 'acp_delete') === 0)
								$privilege_id = 6;
							elseif (strpos($row, 'acp_settings') === 0)
								$privilege_id = 7;
						}
						$db->insert('acl_resources', array('id' => '', 'module_id' => $this->module_id, 'page' => $row, 'params' => '', 'privilege_id' => $privilege_id));
					}
				}
			}

			// Regeln für die Rollen setzen
			$roles = $db->select('id', 'acl_roles');
			$privileges = $db->select('id', 'acl_privileges');
			foreach ($roles as $role) {
				foreach ($privileges as $privilege) {
					$permission = 0;
					if ($role['id'] == 1 && ($privilege['id'] == 1 || $privilege['id'] == 2))
						$permission = 1;
					if ($role['id'] > 1 && $role['id'] < 4)
						$permission = 2;
					if ($role['id'] == 3 && $privilege['id'] == 3)
						$permission = 1;
					if ($role['id'] == 4)
						$permission = 1;

					$db->insert('acl_rules', array('id' => '', 'role_id' => $role['id'], 'module_id' => $this->module_id, 'privilege_id' => $privilege['id'], 'permission' => $permission));
				}
			}

			ACP3_Cache::purge(0, 'acl');

			return true;
		}

		return false;
	}

	/**
	 * Löscht die zu einem Modul zugehörigen Ressourcen
	 *
	 * @return boolean
	 */
	public function removeResources() {
		global $db;

		$bool = $db->delete('acl_resources', 'module_id = ' . $this->module_id);
		$bool2 = $db->delete('acl_rules', 'module_id = ' . $this->module_id);

		ACP3_Cache::purge(0, 'acl');

		return $bool && $bool2;
	}

	abstract public function createTables();

	abstract public function removeTables();

	abstract public function addSettings();

	abstract public function removeSettings();

	abstract public function addToModulesTable();

	abstract public function removeFromModulesTable();
}