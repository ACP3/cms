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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'acl_roles', 'id = \'' . $uri->id . '\'') == 1) {
	$roles = $db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}acl_roles AS p, {pre}acl_roles AS c WHERE p.id = \'' . $uri->id . '\' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');

	if ($uri->action === 'up' && $db->countRows('*', 'acl_roles', 'right_id = ' . ($roles[0]['left_id'] - 1)) > 0) {
		$elem = $db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}acl_roles AS p, {pre}acl_roles AS c WHERE p.right_id = ' . ($roles[0]['left_id'] - 1) . ' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');
		$diff_left = $roles[0]['left_id'] - $elem[0]['left_id'];
		$diff_right = $roles[0]['right_id'] - $elem[0]['right_id'];
	} elseif ($uri->action === 'down' && $db->countRows('*', 'acl_roles', 'left_id = ' . ($roles[0]['right_id'] + 1)) > 0) {
		$elem = $db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}acl_roles AS p, {pre}acl_roles AS c WHERE p.left_id = ' . ($roles[0]['right_id'] + 1) . ' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');
		$diff_left = $elem[0]['left_id'] - $roles[0]['left_id'];
		$diff_right = $elem[0]['right_id'] - $roles[0]['right_id'];
	} else {
		$uri->redirect('errors/404');
	}

	$c_elem = count($elem);
	$c_pages = count($roles);
	$elem_ids = $roles_ids = '';

	for ($i = 0; $i < $c_elem; ++$i) {
		$elem_ids.= 'id = \'' . $elem[$i]['id'] . '\' OR ';
	}
	for ($i = 0; $i < $c_pages; ++$i) {
		$roles_ids.= 'id = \'' . $roles[$i]['id'] . '\' OR ';
	}

	$db->link->beginTransaction();

	if ($uri->action === 'up') {
		$bool = $db->query('UPDATE {pre}acl_roles SET left_id = left_id + ' . $diff_right . ', right_id = right_id + ' . $diff_right . ' WHERE ' . substr($elem_ids, 0, -4), 0);
		$bool2 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - ' . $diff_left . ', right_id = right_id - ' . $diff_left . ' WHERE ' . substr($roles_ids, 0, -4), 0);
	} elseif ($uri->action === 'down') {
		$bool = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - ' . $diff_left . ', right_id = right_id - ' . $diff_left . ' WHERE ' . substr($elem_ids, 0, -4), 0);
		$bool2 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id + ' . $diff_right . ', right_id = right_id + ' . $diff_right . ' WHERE ' . substr($roles_ids, 0, -4), 0);
	}

	$db->link->commit();

	ACP3_Cache::purge(0, 'acl');

	$uri->redirect('acp/access');
} else {
	$uri->redirect('errors/404');
}
