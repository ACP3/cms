<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('newsletter', 'confirm_delete'), uri('acp/newsletter/delete/entries_' . $marked_entries), uri('acp/newsletter'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'nl_accounts', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
		$bool = $db->delete('nl_accounts', 'id = \'' . $entry . '\'');
	}
	$content = combo_box($bool ? lang('newsletter', 'delete_success') : lang('newsletter', 'delete_error'), uri('acp/newsletter'));
} else {
	redirect('errors/404');
}
?>