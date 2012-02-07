<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	view::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	view::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/newsletter/delete_archive/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/newsletter/adm_list_archive')));
} elseif (validate::deleteEntries($entries) === true && $uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->countRows('*', 'newsletter_archive', 'id = \'' . $entry . '\'') == '1') {
			$bool = $db->delete('newsletter_archive', 'id = \'' . $entry . '\'');
		}
	}
	setRedirectMessage($bool !== false ? $lang->t('common', 'delete_success') : $lang->t('common', 'delete_error'), 'acp/newsletter/adm_list_archive');
} else {
	$uri->redirect('errors/404');
}
