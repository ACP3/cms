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
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	$tpl->assign('form', isset($form) ? $form : '');

	$active_modules = $modules->active_modules();
	$mod_list = array();

	// TODO: Selektion der Einträge
	foreach ($active_modules as $name => $dir) {
		if ($dir != 'errors') {
			$mod_list[$name]['name'] = $name;
			$mod_list[$name]['dir'] = $dir;
		}
	}

	$tpl->assign('mod_list', $mod_list);

	$content = $tpl->fetch('access/create.html');
}
?>