<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

/**
 * Baut den String des zu erstellenden /
 * verändernden Zugriffslevels zusammen
 *
 * @param array $modules
 * @return string
 */
function buildAccessLevel($modules)
{
	if (!empty($modules) && is_array($modules)) {
		$modules['errors'] = array('read' => 1, 'create' => 2, 'edit' => 4, 'delete' => 8, 'full' => 16);
		ksort($modules);
		$access_level = '';

		foreach ($modules as $mod => $levels) {
			if (isset($levels['full'])) {
				$level = 16;
			} else {
				$level = 0;
				$level+= isset($levels['read']) ? 1 : 0;
				$level+= isset($levels['create']) ? 2 : 0;
				$level+= isset($levels['edit']) ? 4 : 0;
				$level+= isset($levels['delete']) ? 8 : 0;
			}
			$access_level.= $mod . ':' . $level . ',';
		}
		return substr($access_level, 0, -1);
	}
	return '';
}
/**
 * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
 *
 * @param integer $id
 *  Die ID des zu löschenden Datensatzes
 *
 * @return boolean
 */
function aclDeleteNode($id)
{
	if (!empty($id) && validate::isNumber($id)) {
		global $db;

		$lr = $db->select('left_id, right_id', 'acl_roles', 'id = \'' . $id . '\'');
		if (count($lr) == 1) {
			$db->link->beginTransaction();

			$bool = $db->delete('acl_roles', 'left_id = \'' . $lr[0]['left_id'] . '\'');
			$bool2 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $lr[0]['left_id'] . ' AND ' . $lr[0]['right_id'], 0);
			$bool3 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
			$bool4 = $db->query('UPDATE {pre}acl_roles SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

			$db->link->commit();

			return $bool !== null && $bool2 !== null && $bool3 !== null && $bool4 !== null ? true : false;
		}
	}
	return false;
}