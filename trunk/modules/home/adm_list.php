<?php
/**
 * Administration Home
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['submit']) && !empty($_POST['form']['draft'])) {
	$draft = $db->escape($_POST['form']['draft'], 2);
	$filename = 'modules/home/draft.txt';
	$success = false;

	if (is_file($filename) && is_writable($filename)) {
		$bool = file_put_contents($filename, $draft);
		$success = $bool ? true : false;
	}
	$content = combo_box($success ? lang('home', 'draft_success') : lang('home', 'draft_error'), uri('acp'));
} else {
	if (is_dir('installation/'))
		$tpl->assign('install_dir_exists', true);

	$draft = file_get_contents('modules/home/draft.txt');

	$tpl->assign('draft', $db->escape($draft, 3));

	$content = $tpl->fetch('home/adm_list.html');
}
?>