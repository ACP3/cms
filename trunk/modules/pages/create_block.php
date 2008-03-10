<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$breadcrumb->assign(lang('pages', 'pages'), uri('acp/pages'));
$breadcrumb->assign(lang('pages', 'adm_list_blocks'), uri('acp/pages/adm_list_blocks'));
$breadcrumb->assign(lang('pages', 'create_block'));

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
		$errors[] = lang('pages', 'type_in_index_name');
	if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->select('id', 'pages_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\'', 0, 0, 0, 1) > 0)
		$errors[] = lang('pages', 'index_name_unique');
	if (strlen($form['title']) < 3)
		$errors[] = lang('pages', 'block_title_to_short');

	if (isset($errors)) {
		combo_box($errors);
	} else {
		$insert_values = array(
			'id' => '',
			'index_name' => $db->escape($form['index_name']),
			'title' => $db->escape($form['title']),
		);

		$bool = $db->insert('pages_blocks', $insert_values);

		$content = combo_box($bool ? lang('pages', 'create_block_success') : lang('pages', 'create_block_error'), uri('acp/pages/adm_list_blocks'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('pages/create_block.html');
}
?>