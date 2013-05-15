<?php
namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Ist für die häufig verwendeten Funktionen im Installer zuständig
 *
 * @author Tino Goratsch
 * @package ACP3 Installer
 */
class Functions {

	/**
	 * Gibt eine Box mit den aufgetretenen Fehlern aus
	 *
	 * @param string|array $errors
	 * @return string
	 */
	public static function errorBox($errors) {
		$non_integer_keys = false;
		if (is_array($errors) === true) {
			foreach (array_keys($errors) as $key) {
				if (Core\Validate::isNumber($key) === false) {
					$non_integer_keys = true;
					break;
				}
			}
		} else {
			$errors = (array) $errors;
		}
		$this->injector['View']->assign('error_box', array('non_integer_keys' => $non_integer_keys, 'errors' => $errors));
		return $this->injector['View']->fetch('error_box.tpl');
	}

	/**
	 * Führt die Datenbankschema-Änderungen durch
	 *
	 * @param array $queries
	 * 	Array mit den durchzuführenden Datenbankschema-Änderungen
	 * @param integer $version
	 * 	Version der Datenbank, auf welche aktualisiert werden soll
	 */
	public static function executeSqlQueries(array $queries, $version) {
		$bool = Core\ModuleInstaller::executeSqlQueries($queries);

		$result = array(
			'text' => sprintf($this->injector['Lang']->t('update_db_version_to'), $version),
			'class' => $bool === true ? 'success' : 'important',
			'result_text' => $this->injector['Lang']->t($bool === true ? 'db_update_success' : 'db_update_error')
		);

		return $result;
	}

	/**
	 * Generiert ein gesalzenes Passwort
	 *
	 * @param string $salt
	 * @param string $plaintext
	 * @param string $algorithm
	 * @return string
	 */
	public static function generateSaltedPassword($salt, $plaintext, $algorithm = 'sha1') {
		return hash($algorithm, $salt . hash($algorithm, $plaintext));
	}

	/**
	 * Liefert ein Array mit allen Zeitzonen dieser Welt aus
	 *
	 * @param string $current_value
	 * @return array
	 */
	public static function getTimeZones($current_value = '') {
		$timeZones = array(
			'Africa' => \DateTimeZone::listIdentifiers(\DateTimeZone::AFRICA),
			'America' => \DateTimeZone::listIdentifiers(\DateTimeZone::AMERICA),
			'Antarctica' => \DateTimeZone::listIdentifiers(\DateTimeZone::ANTARCTICA),
			'Arctic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ARCTIC),
			'Asia' => \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA),
			'Atlantic' => \DateTimeZone::listIdentifiers(\DateTimeZone::ATLANTIC),
			'Australia' => \DateTimeZone::listIdentifiers(\DateTimeZone::AUSTRALIA),
			'Europe' => \DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE),
			'Indian' => \DateTimeZone::listIdentifiers(\DateTimeZone::INDIAN),
			'Pacitic' => \DateTimeZone::listIdentifiers(\DateTimeZone::PACIFIC),
			'UTC' => \DateTimeZone::listIdentifiers(\DateTimeZone::UTC),
		);

		foreach ($timeZones as $key => $values) {
			$i = 0;
			foreach ($values as $row) {
				unset($timeZones[$key][$i]);
				$timeZones[$key][$row]['selected'] = Core\Functions::selectEntry('date_time_zone', $row, $current_value);
				++$i;
			}
		}
		return $timeZones;
	}

	/**
	 * Führt die Installationsanweisungen des jeweiligen Moduls durch
	 *
	 * @param string $module
	 * @return boolean
	 */
	public static function installModule($module) {
		$bool = false;

		$module = ucfirst($module);
		$path = MODULES_DIR . $module . '/' . $module . 'Installer.php';
		if (is_file($path) === true) {
			$className = Core\ModuleInstaller::buildClassName($module);
			$install = new $className(\ACP3\Installer\Installer::$injector);
			if ($install instanceof \ACP3\Core\ModuleInstaller) {
				$bool = $install->install();
			}
		}

		return $bool;
	}

	/**
	 * Generiert das Dropdown-Menü mit der zur Verfügung stehenden Installersprachen
	 *
	 * @param string $selected_language
	 * @return array
	 */
	public static function languagesDropdown($selected_language) {
		// Dropdown-Menü für die Sprachen
		$languages = array();
		$path = ACP3_ROOT_DIR . 'installation/languages/';
		$files = scandir($path);
		foreach ($files as $row) {
			if ($row !== '.' && $row !== '..') {
				$lang_info = \ACP3\Core\XML::parseXmlFile($path . $row, '/language/info');
				if (!empty($lang_info)) {
					$languages[] = array(
						'language' => substr($row, 0, -4),
						'selected' => $selected_language === substr($row, 0, -4) ? ' selected="selected"' : '',
						'name' => $lang_info['name']
					);
				}
			}
		}
		return $languages;
	}

	/**
	 * Liefert ein Array zur Ausgabe als Dropdown-Menü
	 * für die Anzahl der anzuzeigenden Datensätze je Seite
	 *
	 * @param integer $current_value
	 * @param integer $steps
	 * @param integer $max_value
	 * @return array
	 */
	public static function recordsPerPage($current_value, $steps = 5, $max_value = 50) {
		// Einträge pro Seite
		$records = array();
		for ($i = 0, $j = $steps; $j <= $max_value; $i++, $j+= $steps) {
			$records[$i]['value'] = $j;
			$records[$i]['selected'] = Core\Functions::selectEntry('entries', $j, $current_value);
		}
		return $records;
	}

	/**
	 * Setzt die Ressourcen-Tabelle auf die Standardwerte zurück
	 */
	public static function resetResources($mode = 1) {
		\ACP3\Installer\Installer::$injector['Db']->executeUpdate('TRUNCATE TABLE ' . DB_PRE . 'acl_resources');

		// Moduldaten in die ACL schreiben
		$modules = scandir(MODULES_DIR);
		foreach ($modules as $module) {
			$path = MODULES_DIR . $module . '/' . $module . 'Installer.php';
			if ($module !== '.' && $module !== '..' && is_file($path) === true) {
				$className = \ACP3\Core\ModuleInstaller::buildClassName($module);
				$install = new $className(\ACP3\Installer\Installer::$injector);
				$install->addResources($mode);
			}
		}
	}

	/**
	 * Generiert einen Zufallsstring beliebiger Länge
	 *
	 * @param integer $str_length
	 *  Länge des zufälligen Strings
	 * @return string
	 */
	public static function salt($str_length) {
		$salt = '';
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$c_chars = strlen($chars) - 1;
		while (strlen($salt) < $str_length) {
			$char = $chars[mt_rand(0, $c_chars)];
			// Zeichen nur hinzufügen, wenn sich dieses nicht bereits im Salz befindet
			if (strpos($salt, $char) === false) {
				$salt.= $char;
			}
		}
		return $salt;
	}

	/**
	 * Führt die Updateanweisungen eines Moduls aus
	 *
	 * @param string $module
	 * @return integer
	 */
	public static function updateModule($module) {
		$result = false;

		$module = ucfirst($module);
		$path = MODULES_DIR . $module . '/' . $module . 'Installer.php';
		if (is_file($path) === true) {
			$className = Core\ModuleInstaller::buildClassName($module);
			$install = new $className(\ACP3\Installer\Installer::$injector);
			if ($install instanceof \ACP3\Core\ModuleInstaller &&
					(ACP3_Modules::isInstalled($module) || count($install->renameModule()) > 0)) {
				$result = $install->updateSchema();
			}
		}

		return $result;
	}

	/**
	 * Schreibt die Systemkonfigurationsdatei
	 *
	 * @param array $data
	 * @return boolean
	 */
	public static function writeConfigFile(array $data) {
		$path = ACP3_DIR . 'config.php';
		if (is_writable($path) === true) {
			// Konfigurationsdatei in ein Array schreiben
			ksort($data);

			$content = "<?php\n";
			$content.= "define('INSTALLED', true);\n";
			if (defined('DEBUG') === true)
				$content.= "define('DEBUG', " . ((bool) DEBUG === true ? 'true' : 'false') . ");\n";
			$pattern = "define('CONFIG_%s', %s);\n";
			foreach ($data as $key => $value) {
				if (is_numeric($value) === true)
					$value = $value;
				elseif (is_bool($value) === true)
					$value = $value === true ? 'true' : 'false';
				else
					$value = '\'' . $value . '\'';
				$content.= sprintf($pattern, strtoupper($key), $value);
			}
			$bool = @file_put_contents($path, $content, LOCK_EX);
			return $bool !== false ? true : false;
		}
		return false;
	}

	/**
	 * Erstellt/Verändert die Konfigurationsdateien für die Module
	 *
	 * @param string $module
	 * @param array $data
	 * @return boolean
	 */
	public static function writeSettingsToDb($module, $data)
	{
		$bool = false;
		$mod_id = \ACP3\Installer\Installer::$injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));
		if (!empty($mod_id)) {
			foreach ($data as $key => $value) {
				$bool = \ACP3\Installer\Installer::$injector['Db']->executeUpdate('UPDATE ' . DB_PRE . 'settings SET value = ? WHERE module_id = ? AND name = ?', array($value, (int) $mod_id, $key));
			}
		}

		return $bool !== false ? true : false;
	}
}