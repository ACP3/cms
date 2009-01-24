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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'menu_items', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == 1) {
	// Brotkrümelspur setzen
	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('menu_items', 'menu_items'), uri('acp/menu_items'));
	breadcrumb::assign($lang->t('common', 'edit_order'));

	switch ($uri->mode) {
		case 'up':
			$pages = $db->query('SELECT c.id, c.block_id, c.left_id, c.right_id FROM ' . CONFIG_DB_PRE . 'menu_items AS p, ' . CONFIG_DB_PRE . 'menu_items AS c WHERE p.id = \'' . $uri->id . '\' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id');
			if ($db->select('COUNT(id)', 'menu_items', 'right_id < ' . $pages[0]['left_id'] . ' AND block_id = \'' . $pages[0]['block_id'] . '\'', 0, 0, 0, 1) > 0) {
				$elem = $db->query('SELECT c.id, c.left_id, c.right_id FROM ' . CONFIG_DB_PRE . 'menu_items AS p, ' . CONFIG_DB_PRE . 'menu_items AS c WHERE p.right_id = ' . $pages[0]['left_id'] . ' - 1 AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id');
				$c_elem = count($elem);
				$c_pages = count($pages);
				$diff = $pages[0]['right_id'] - $pages[0]['left_id'] + 1;

				for ($i = 0; $i < $c_elem; ++$i) {
					$update_values = array(
						'left_id' => $elem[0]['left_id'] + $i + $diff + ($i > 1 ? 1 : 0),
						'right_id' => $elem[0]['left_id'] + $i + $diff + $elem[$i]['right_id'] - $elem[$i]['left_id'] + ($i > 1 ? 1 : 0),
					);
					$db->update('menu_items', $update_values, 'id = \'' . $elem[$i]['id'] . '\'');
				}
				for ($i = 0; $i < $c_pages; ++$i) {
					$update_values = array(
						'left_id' => $elem[0]['left_id'] + $i + ($i > 1 ? 1 : 0),
						'right_id' => $elem[0]['left_id'] + $i + $pages[$i]['right_id'] - $pages[$i]['left_id'] + ($i > 1 ? 1 : 0),
					);
					$db->update('menu_items', $update_values, 'id = \'' . $pages[$i]['id'] . '\'');
				}
			}
			break;
		case 'down':
			$pages = $db->query('SELECT c.id, c.block_id, c.left_id, c.right_id FROM ' . CONFIG_DB_PRE . 'menu_items AS p, ' . CONFIG_DB_PRE . 'menu_items AS c WHERE p.id = \'' . $uri->id . '\' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id');
			if ($db->select('COUNT(id)', 'menu_items', 'left_id > ' . $pages[0]['right_id'] . ' AND block_id = \'' . $pages[0]['block_id'] . '\'', 0, 0, 0, 1) > 0) {
				$elem = $db->query('SELECT c.id, c.left_id, c.right_id FROM ' . CONFIG_DB_PRE . 'menu_items AS p, ' . CONFIG_DB_PRE . 'menu_items AS c WHERE p.left_id = ' . $pages[0]['right_id'] . ' + 1 AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id');
				$c_elem = count($elem);
				$c_pages = count($pages);
				$diff = $elem[0]['right_id'] - $elem[0]['left_id'] + 1;

				for ($i = 0; $i < $c_elem; ++$i) {
					$update_values = array(
						'left_id' => $pages[0]['left_id'] + $i + ($i > 1 ? 1 : 0),
						'right_id' => $pages[0]['left_id'] + $i + $elem[$i]['right_id'] - $elem[$i]['left_id'] + ($i > 1 ? 1 : 0),
					);
					$db->update('menu_items', $update_values, 'id = \'' . $elem[$i]['id'] . '\'');
				}
				for ($i = 0; $i < $c_pages; ++$i) {
					$update_values = array(
						'left_id' => $pages[0]['left_id'] + $i + $diff + ($i > 1 ? 1 : 0),
						'right_id' => $pages[0]['left_id'] + $i + $diff + $pages[$i]['right_id'] - $pages[$i]['left_id'] + ($i > 1 ? 1 : 0),
					);
					$db->update('menu_items', $update_values, 'id = \'' . $pages[$i]['id'] . '\'');
				}
			}
			break;
		default:
			$error = true;
	}
	if (!isset($error)) {
		setNavbarCache();

		redirect('acp/menu_items');
	}
}
redirect('acp/errors/404');
?>