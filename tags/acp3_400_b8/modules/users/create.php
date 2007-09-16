<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/users/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$access = $db->select('id, name', 'access', 0, 'name ASC');
	$c_access = count($access);

	for ($i = 0; $i < $c_access; $i++) {
		$access[$i]['name'] = $access[$i]['name'];
		$access[$i]['selected'] = select_entry('access', $access[$i]['id']);
	}
	$tpl->assign('access', $access);

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('users/create.html');
}
?>