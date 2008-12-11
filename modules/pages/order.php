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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'pages', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == 1) {
	// Brotkrümelspur setzen
	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('pages', 'pages'), uri('acp/pages'));
	breadcrumb::assign($lang->t('common', 'edit_order'));
	$elem_fields = 'left_id, right_id, block_id';
	$pre_fields = 'id, left_id, right_id, block_id';

	switch ($uri->mode) {
		case 'up':
			if ($db->select('COUNT(id)', 'pages', 'id != \'' . $uri->id . '\' AND left_id < (SELECT left_id FROM ' . CONFIG_DB_PRE . 'pages WHERE id = \'' . $uri->id . '\')', 0, 0, 0, 1) > 0) {
				$elem = $db->select($elem_fields, 'pages', 'id = \'' . $uri->id . '\'');
				$pre = $db->select($pre_fields, 'pages', 'id != \'' . $uri->id . '\' AND left_id < \'' . $elem[0]['left_id'] . '\'', 'left_id DESC', 1);
			} else {
				$error = true;
			}
			break;
		case 'down':
			if ($db->select('COUNT(id)', 'pages', 'id != \'' . $uri->id . '\' AND left_id > (SELECT left_id FROM ' . CONFIG_DB_PRE . 'pages WHERE id = \'' . $uri->id . '\')', 0, 0, 0, 1) > 0) {
				$elem = $db->select($elem_fields, 'pages', 'id = \'' . $uri->id . '\'');
				$pre = $db->select($pre_fields, 'pages', 'id != \'' . $uri->id . '\' AND left_id > \'' . $elem[0]['left_id'] . '\'', 'left_id ASC', 1);
			} else {
				$error = true;
			}
			break;
		default:
			$error = true;
	}
	// Sortierung aktualisieren
	if (!isset($error)) {
		$db->update('pages', array('left_id' => $pre[0]['left_id'], 'right_id' => $pre[0]['right_id'], 'block_id' => $pre[0]['block_id']), 'id = \'' . $uri->id . '\'');
		$db->update('pages', array('left_id' => $elem[0]['left_id'], 'right_id' => $elem[0]['right_id'], 'block_id' => $elem[0]['block_id']), 'id = \'' . $pre[0]['id'] . '\'');
		setNavbarCache();

		redirect('acp/pages');
	}
}
if (isset($error)) {
	redirect('errors/404');
}
?>