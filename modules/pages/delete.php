<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array($lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox($lang->t('pages', 'confirm_delete'), uri('acp/pages/delete/entries_' . $marked_entries), uri('acp/pages'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = $bool2 = $bool3 = $bool4 = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'pages', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$lr = $db->select('left_id, right_id', 'pages', 'id = \'' . $entry . '\'');

			$bool = $db->delete('pages', 'left_id = \'' . $lr[0]['left_id'] . '\'');
			$bool2 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $lr[0]['left_id'] . ' AND ' . $lr[0]['right_id'], 0);
			$bool3 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
			$bool4 = $db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

			cache::delete('pages_list_id_' . $entry);
		}
	}
	setNavbarCache();

	$content = comboBox($bool && $bool2 && $bool3 && $bool4 ? $lang->t('pages', 'delete_success') : $lang->t('pages', 'delete_error'), uri('acp/pages'));
}
?>