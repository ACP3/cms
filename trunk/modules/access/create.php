<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = lang('common', 'name_to_short');
	if (!empty($form['name']) && $db->select('id', 'access', 'name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
		$errors[] = lang('access', 'access_level_already_exist');
	// Überprüfen, ob zumindest einem Modul ein Zugriffslevel zugewiesen wurde
	$empty = true;
	foreach ($form['modules'] as $key) {
		if (!empty($key)) {
			$empty = false;
			break;
		}
	}
	if ($empty)
		$errors[] = lang('access', 'select_modules');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		// String für die einzelnen Zugriffslevel auf die Module erstellen
		$form['modules']['errors'] = '2';
		ksort($form['modules']);
		$insert_mods = '';

		foreach ($form['modules'] as $module => $level) {
			$insert_mods.= $module . ':' . $level . ',';
		}

		$insert_values = array(
			'id' => '',
			'name' => $db->escape($form['name']),
			'modules' => substr($insert_mods, 0, -1),
		);

		$bool = $db->insert('access', $insert_values);

		$content = comboBox($bool ? lang('access', 'create_success') : lang('access', 'create_error'), uri('acp/access'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$mod_list = $modules->modulesList();

	function select_level($dir, $value)
	{
		if (isset($_POST['form']['modules'][$dir]) && $_POST['form']['modules'][$dir] == $value) {
			return ' selected="selected"';
		}
		return '';
	}

	foreach ($mod_list as $name => $info) {
		if ($info['dir'] == 'errors' || !$info['active']) {
			unset($mod_list[$name]);
		} else {
			$mod_list[$name]['level_0_selected'] = select_level($info['dir'], '0');
			$mod_list[$name]['level_1_selected'] = select_level($info['dir'], '1');
			$mod_list[$name]['level_2_selected'] = select_level($info['dir'], '2');
		}
	}
	$tpl->assign('mod_list', $mod_list);

	$content = $tpl->fetch('access/create.html');
}
?>