<?php
/**
 ** Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries))
	$entries = $uri->entries;

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('access', 'access'), $uri->route('acp/access'));
breadcrumb::assign($lang->t('access', 'adm_list_resources'), $uri->route('acp/access/adm_list_resources'));
breadcrumb::assign($lang->t('access', 'delete_resources'));

if (!isset($entries)) {
	view::setContent(comboBox(array($lang->t('common', 'no_entries_selected'))));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	view::setContent(comboBox($lang->t('common', 'confirm_delete'), $uri->route('acp/access/delete_resources/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/access/adm_list_resources')));
} elseif (validate::deleteEntries($entries) && $uri->action == 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = null;

	foreach ($marked_entries as $entry) {
		$bool = $db->delete('acl_resources', 'id = \'' . $entry . '\'');
	}

	require_once MODULES_DIR . 'access/functions.php';
	acl::setResourcesCache();

	$text = $bool !== null ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error');
	view::setContent(comboBox($text, $uri->route('acp/access/adm_list_resources')));
} else {
	$uri->redirect('acp/errors/404');
}
