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

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $modules->entries))
	$entries = $modules->entries;

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox(lang('pages', 'confirm_delete'), uri('acp/pages/delete/entries_' . $marked_entries), uri('acp/pages'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $modules->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->isNumber($entry) && $db->select('id', 'pages', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('pages', 'id = \'' . $entry . '\'');
			$cache->delete('pages_list_id_' . $entry);
		}
	}

	$cache->create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.uri, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));

	$content = comboBox($bool ? lang('pages', 'delete_success') : lang('pages', 'delete_error'), uri('acp/pages'));
}
?>