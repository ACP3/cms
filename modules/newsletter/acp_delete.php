<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries($uri->entries) === true)
	$entries = $uri->entries;

if (!isset($entries)) {
	ACP3_View::setContent(errorBox($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_View::setContent(confirmBox($lang->t('common', 'confirm_delete'), $uri->route('acp/newsletter/delete_archive/entries_' . $marked_entries . '/action_confirmed/'), $uri->route('acp/newsletter')));
} elseif ($uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		$bool = $db->delete('newsletter_archive', 'id = \'' . $entry . '\'');
	}
	setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter');
} else {
	$uri->redirect('errors/404');
}
