<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im Installer zuständig
 *
 * @author Tino Goratsch
 * @package ACP3 Installer
 */

/**
 * Generiert ein gesalzenes Passwort
 *
 * @param string $salt
 * @param string $plaintext
 * @param string $algorithm
 * @return string
 */
function generateSaltedPassword($salt, $plaintext, $algorithm = 'sha1')
{
	return hash($algorithm, $salt . hash($algorithm, $plaintext));
}
/**
 * Liefert ein Array mit allen Zeitzonen dieser Welt aus
 *
 * @param string $current_value
 * @return array
 */
function getTimeZones($current_value = '')
{
	$timeZones = array(
		'Africa' => DateTimeZone::listIdentifiers(DateTimeZone::AFRICA),
		'America' => DateTimeZone::listIdentifiers(DateTimeZone::AMERICA),
		'Antarctica' => DateTimeZone::listIdentifiers(DateTimeZone::ANTARCTICA),
		'Arctic' => DateTimeZone::listIdentifiers(DateTimeZone::ARCTIC),
		'Asia' => DateTimeZone::listIdentifiers(DateTimeZone::ASIA),
		'Atlantic' => DateTimeZone::listIdentifiers(DateTimeZone::ATLANTIC),
		'Australia' => DateTimeZone::listIdentifiers(DateTimeZone::AUSTRALIA),
		'Europe' => DateTimeZone::listIdentifiers(DateTimeZone::EUROPE),
		'Indian' => DateTimeZone::listIdentifiers(DateTimeZone::INDIAN),
		'Pacitic' => DateTimeZone::listIdentifiers(DateTimeZone::PACIFIC),
		'UTC' => DateTimeZone::listIdentifiers(DateTimeZone::UTC),
	);

	foreach ($timeZones as $key => $values) {
		$i = 0;
		foreach ($values as $row) {
			unset($timeZones[$key][$i]);
			$timeZones[$key][$row]['selected'] = selectEntry('date_time_zone', $row, $current_value);
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
function installModule($module)
{
	$bool = false;

	$path = MODULES_DIR . $module . '/install.class.php';
	if (is_file($path) === true) {
		require $path;
		$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $module)))) . 'ModuleInstaller';
		$install = new $className();
		if ($install instanceof ACP3_ModuleInstaller) {
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
function languagesDropdown($selected_language)
{
	// Dropdown-Menü für die Sprachen
	$languages = array();
	$path = ACP3_ROOT . 'installation/languages/';
	$files = scandir($path);
	foreach ($files as $row) {
		if ($row !== '.' && $row !== '..') {
			$lang_info = ACP3_XML::parseXmlFile($path . $row, '/language/info');
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
function recordsPerPage($current_value, $steps = 5, $max_value = 50)
{
	// Einträge pro Seite
	$records = array();
	for ($i = 0, $j = $steps; $j <= $max_value; $i++, $j+= $steps) {
		$records[$i]['value'] = $j;
		$records[$i]['selected'] = selectEntry('entries', $j, $current_value);
	}
	return $records;
}
/**
 * Generiert einen Zufallsstring beliebiger Länge
 *
 * @param integer $str_length
 *  Länge des zufälligen Strings
 * @return string
 */
function salt($str_length)
{
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
 * Selektion eines Eintrages in einem Dropdown-Menü
 *
 * @param string $name
 *  Name des Feldes im Formular
 * @param mixed $defValue
 *  Abzugleichender Parameter mit $currentvalue
 * @param mixed $currentValue
 *  Wert aus der SQL Tabelle
 * @param string $attr
 *  HTML-Attribut, um Eintrag zu selektieren
 * @return string
 */
function selectEntry($name, $defValue, $currentValue = '', $attr = 'selected')
{
	$attr = ' ' . $attr . '="' . $attr . '"';

	if (isset($_POST[$name])) {
		if (is_array($_POST[$name]) === false && $_POST[$name] == $defValue) {
			return $attr;
		} elseif (is_array($_POST[$name]) === true) {
			foreach ($_POST[$name] as $row) {
				if ($row == $defValue)
					return $attr;
			}
		}
	} else {
		if (is_array($currentValue) === false && $currentValue == $defValue) {
			return $attr;
		} elseif (is_array($currentValue) === true) {
			foreach ($currentValue as $row) {
				if ($row == $defValue)
					return $attr;
			}
		}
		return '';
	}
}
/**
 * Führt die Updateanweisungen eines Moduls aus
 *
 * @param string $module
 * @return integer
 */
function updateModule($module)
{
	$result = false;

	$path = MODULES_DIR . $module . '/install.class.php';
	if (is_file($path) === true) {
		require $path;
		$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $module)))) . 'ModuleInstaller';
		$install = new $className();
		if ($install instanceof ACP3_ModuleInstaller &&
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
function writeConfigFile(array $data)
{
	$path = INCLUDES_DIR . 'config.php';
	if (is_writable($path) === true){
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