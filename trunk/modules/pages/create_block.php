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
	include 'modules/pages/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('pages/create_block.html');
}
?>