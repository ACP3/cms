<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (!empty($modules->id) && $db->select('id', 'categories', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/categories/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$category = $db->select('name, description', 'categories', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $category[0]);

		$content = $tpl->fetch('categories/acp_edit.html');
	}
} else {
	redirect('errors/404');
}
?>