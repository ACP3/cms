<?php
/**
 * Diese Datei ist für die häufig verwendeten Funktionen im Installer zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3 Installer
 */
// Sprachdateien
function lang($key)
{
	static $lang_data = array();

	$path = 'languages/' . LANG . '/lang.xml';

	if (!isset($lang_data[$key]) && is_file($path)) {
		$xml = simplexml_load_file($path);
		foreach ($xml->item as $row) {
			$lang_data[(string) $row->name] = (string) $row->message;
		}
	}

	return isset($lang_data[$key]) ? $lang_data[$key] : strtoupper('{' . $key . '}');
}
// Variablen escapen
function mask($var, $mode = 1)
{
	switch ($mode) {
		case 1:
			return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
			break;
		case 2:
			return addslashes($var);
			break;
		case 3:
			return stripslashes($var);
			break;
	}
}
// Funktion zum Salzen von Passwörtern
function salt($str_length)
{
	$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$c_chars = strlen($chars) - 1;
	$key = '';
	for ($i = 0; $i < $str_length; ++$i) {
		$key.= $chars[mt_rand(0, $c_chars)];
	}
	return $key;
}
// Selektion eines Eintrages in einem Dropdown-Menü
function select_entry($name, $value, $field_value = '', $attr = 'selected') {
	$attr = ' ' . $attr . '="' . $attr . '"';

	if (!isset($_POST['form'][$name])) {
		if (!is_array($field_value) && $field_value == $value) {
			return $attr;
		} elseif (is_array($field_value)) {
			foreach ($field_value as $row) {
				if ($row == $value)
					return $attr;
			}
		}
	} elseif (isset($_POST['form'][$name]) && $_POST['form'][$name] != '') {
		if (!is_array($_POST['form'][$name]) && $_POST['form'][$name] == $value) {
			return $attr;
		} elseif (is_array($_POST['form'][$name])) {
			foreach ($_POST['form'][$name] as $row) {
				if ($row == $value)
					return $attr;
			}
		}
	}
}
// Konfigurationsdateien für die Module erstellen
function write_config($module, $data)
{
	$path = '../modules/' . $module . '/module.xml';
	if (!preg_match('=/=', $module) && is_file($path)) {
		$xml = DOMDocument::load($path);
		$xp = new domxpath($xml);
		$items = $xp->query('settings/*');
		$i = $items->length - 1;

		while ($i > -1) {
			$item = $items->item($i);

			if (array_key_exists($item->nodeName, $data)) {
				$newitem = $xml->createElement($item->nodeName);
				$newitem_content = $xml->createCDATASection($data[$item->nodeName]);
				$newitem->appendChild($newitem_content);
				$item->parentNode->replaceChild($newitem, $item);
			}
			$i--;
		}
		$bool = $xml->save($path);

		return $bool ? true : false;
	}
	return false;
}
?>