<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\w|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('comments', 'confirm_delete'), uri('acp/comments/delete_comments_per_module/entries_' . $marked_entries), uri('acp/comments'));
} elseif (preg_match('/^([\w|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && preg_match('/^(\w+)$/', $entry) && $db->select('COUNT(id)', 'comments', 'module = \'' . $entry . '\'', 0, 0, 0, 1) > '0') {
			$bool = $db->delete('comments', 'module = \'' . $entry . '\'');
		}
	}
	$content = comboBox($bool ? $lang->t('comments', 'delete_success') : $lang->t('comments', 'delete_error'), uri('acp/comments'));
}
?>