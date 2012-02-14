<?php
/**
 ** Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

/**
 * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
 *
 * @param integer $id
 *  Die ID des zu löschenden Datensatzes
 * @return boolean
 */
function aclDeleteNode($id)
{
	if (!empty($id) && validate::isNumber($id) === true) {
		global $db;

		$lr = $db->select('left_id, right_id', 'acl_roles', 'id = \'' . $id . '\'');
		if (count($lr) === 1) {
			$db->link->beginTransaction();

			$bool = $db->delete('acl_roles', 'left_id = \'' . $lr[0]['left_id'] . '\'');
			$bool2 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - 1, right_id = right_id - 1 WHERE left_id BETWEEN ' . $lr[0]['left_id'] . ' AND ' . $lr[0]['right_id'], 0);
			$bool3 = $db->query('UPDATE {pre}acl_roles SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
			$bool4 = $db->query('UPDATE {pre}acl_roles SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

			$db->link->commit();

			return $bool !== false && $bool2 !== false && $bool3 !== false && $bool4 !== false ? true : false;
		}
	}
	return false;
}
/**
 * Sorgt dafür, das ein Knoten in einen anderen Block verschoben werden kann
 *
 * @param integer $id
 *	ID des zu verschiebenden Knotens
 * @param integer $parent
 *	ID des neuen Elternelements
 * @param array $update_values
 * @return
 */
function aclEditNode($id, $parent, array $update_values)
{
	global $db;

	if (validate::isNumber($id) === true && (validate::isNumber($parent) || $parent == '')) {
		// Die aktuelle Seite mit allen untergeordneten Seiten selektieren
		$roles = $db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}acl_roles AS p, {pre}acl_roles AS c WHERE p.id = \'' . $id . '\' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');

		// Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
		if (empty($parent) && $db->countRows('*', 'acl_roles', 'left_id < ' . $roles[0]['left_id'] . ' AND right_id > ' . $roles[0]['right_id']) == 0) {
			$bool = $db->update('acl_roles', $update_values, 'id = \'' . $id . '\'');
		} else {
			// Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
			$chk_parent = $db->query('SELECT id FROM {pre}acl_roles WHERE left_id < ' . $pages[0]['left_id'] . ' AND right_id > ' . $pages[0]['right_id'] . ' ORDER BY left_id DESC LIMIT 1');
			if (isset($chk_parent[0]) && $chk_parent[0]['id'] == $parent) {
				$bool = $db->update('acl_roles', $update_values, 'id = \'' . $id . '\'');
			// ...ansonsten den Baum bearbeiten...
			} else {
				$bool = false;
				// Differenz zwischen linken und rechten Wert bilden
				$page_diff = $roles[0]['right_id'] - $roles[0]['left_id'] + 1;

				// Neues Elternelement
				$new_parent = $db->select('left_id, right_id', 'acl_roles', 'id = \'' . $parent . '\'');

				// Rekursion verhindern
				if (!empty($new_parent) && $new_parent[0]['left_id'] < $roles[0]['left_id'] && $new_parent[0]['right_id'] > $roles[0]['right_id']) {
					$bool = false;
				} else {
					// Knoten werden eigenes Root-Element
					if (empty($new_parent)) {
						$new_parent = $db->select('MAX(right_id) AS right_id', 'acl_roles', 'block_id =  \'' . $roles[0]['block_id'] . '\'');

						$diff = $new_parent[0]['right_id'] - $roles[0]['right_id'];

						$db->link->beginTransaction();
						$db->query('UPDATE {pre}acl_roles SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $roles[0]['left_id'] . ' AND right_id > ' . $roles[0]['right_id'], 0);
						$db->query('UPDATE {pre}acl_roles SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $roles[0]['right_id'] . ' AND block_id = \'' . $roles[0]['block_id'] . '\'', 0);
					// Knoten werden wieder Kinder von einem anderen Knoten
					} else {
						// Teilbaum nach unten...
						if ($new_parent[0]['left_id'] > $roles[0]['left_id']) {
							$new_parent[0]['left_id'] = $new_parent[0]['left_id'] - $page_diff;
							$new_parent[0]['right_id'] = $new_parent[0]['right_id'] - $page_diff;
						}

						$diff = $new_parent[0]['left_id'] - $roles[0]['left_id'] + 1;

						$db->link->beginTransaction();
						$db->query('UPDATE {pre}acl_roles SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $roles[0]['left_id'] . ' AND right_id > ' . $roles[0]['right_id'], 0);
						$db->query('UPDATE {pre}acl_roles SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $roles[0]['right_id'], 0);
						$db->query('UPDATE {pre}acl_roles SET right_id = right_id + ' . $page_diff . ' WHERE left_id <= ' . $new_parent[0]['left_id'] . ' AND right_id >= ' . $new_parent[0]['right_id'], 0);
						$db->query('UPDATE {pre}acl_roles SET left_id = left_id + ' . $page_diff . ', right_id = right_id + ' . $page_diff . ' WHERE left_id > ' . $new_parent[0]['left_id'], 0);
					}

					// Einträge aktualisieren
					$c_roles = count($roles);
					for ($i = 0; $i < $c_roles; ++$i) {
						$bool = $db->query('UPDATE {pre}acl_roles SET left_id = ' . ($roles[$i]['left_id'] + $diff) . ', right_id = ' . ($roles[$i]['right_id'] + $diff) . ' WHERE id = \'' . $roles[$i]['id'] . '\'', 0);
						if ($bool === false)
							break;
					}
					$db->update('acl_roles', $update_values, 'id = \'' . $id . '\'');
					$db->link->commit();
				}
			}
		}
		return $bool;
	}
	return false;
}
/**
 * Erstellt einen neuen Knoten
 *
 * @param integer $parent_id
 *	ID der übergeordneten Rolle
 * @param array $insert_values
 * @return boolean
 */
function aclInsertNode($parent_id, array $insert_values)
{
	if (validate::isNumber($parent_id) === true) {
		global $db;

		$parent = $db->select('left_id, right_id', 'acl_roles', 'id = \'' . $parent_id . '\'');

		$db->query('UPDATE {pre}acl_roles SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $parent[0]['right_id'], 0);
		$db->query('UPDATE {pre}acl_roles SET right_id = right_id + 2 WHERE left_id <= ' . $parent[0]['left_id'] . ' AND right_id >= ' . $parent[0]['right_id'], 0);

		$insert_values['left_id'] = $parent[0]['right_id'];
		$insert_values['right_id'] = $parent[0]['right_id'] + 1;

		return $db->insert('acl_roles', $insert_values);
	}
}