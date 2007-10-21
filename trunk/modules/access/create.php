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
	include 'modules/access/entry.php';
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