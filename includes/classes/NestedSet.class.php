<?php
/**
 * Klasse zum Erstellen, Bearbeiten, Löschen und
 * Umsortieren von Knoten in einem Nested Set Baum
 *
 * @author Tino Goratsch
 */
class ACP3_NestedSet {

	/**
	 * Der Tabellenname
	 * @var string
	 */
	private $table_name;

	/**
	 * Legt fest, ob das Block-Management aktiv ist oder nicht
	 * @var boolean
	 */
	private $enable_blocks;

	/**
	 * 
	 * @param string $table_name
	 */
	public function __construct($table_name, $enable_blocks = false) {
		$this->table_name = $table_name;
		$this->enable_blocks = $enable_blocks;
	}

	/**
	 * Löscht einen Knoten und verschiebt seine Kinder eine Ebene nach oben
	 *
	 * @param integer $id
	 *  Die ID des zu löschenden Datensatzes
	 * @return boolean
	 */
	function deleteNode($id) {
		if (!empty($id) && ACP3_Validate::isNumber($id) === true) {
			$lr = ACP3_CMS::$db->select('left_id, right_id', $this->table_name, 'id = ' . $id);
			if (count($lr) === 1) {
				ACP3_CMS::$db->link->beginTransaction();

				// Die aktuelle Seite mit allen untergeordneten Seiten selektieren
				$items = ACP3_CMS::$db->query('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM {pre}' . $this->table_name . ' AS p, {pre}' . $this->table_name . ' AS n WHERE p.id = ' . $id . ' AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id ASC');
				$c_items = count($items);

				$bool = ACP3_CMS::$db->delete($this->table_name, 'left_id = \'' . $lr[0]['left_id'] . '\'');
				// root_id und parent_id der Kinder aktualisieren
				for ($i = 1; $i < $c_items; ++$i) {
					$root_id = ACP3_CMS::$db->query('SELECT id FROM {pre}' . $this->table_name . ' WHERE left_id < ' . $items[$i]['left_id'] . ' AND right_id >= ' . $items[$i]['right_id'] . ' ORDER BY left_id ASC LIMIT 1');
					$parent = ACP3_CMS::$db->query('SELECT id FROM {pre}' . $this->table_name . ' WHERE left_id < ' . $items[$i]['left_id'] . ' AND right_id >= ' . $items[$i]['right_id'] . ' ORDER BY left_id DESC LIMIT 1');
					ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET root_id = ' . (!empty($root_id[0]['id']) ? $root_id[0]['id'] : $items[$i]['id']) . ', parent_id = ' . (!empty($parent[0]['id']) ? $parent[0]['id'] : 0) . ', left_id = left_id - 1, right_id = right_id - 1 WHERE id = ' . $items[$i]['id'], 0);
				}

				$bool2 = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - 2 WHERE left_id > ' . $lr[0]['right_id'], 0);
				$bool3 = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id - 2 WHERE right_id > ' . $lr[0]['right_id'], 0);

				ACP3_CMS::$db->link->commit();

				return $bool !== false && $bool2 !== false && $bool3 !== false ? true : false;
			}
		}
		return false;
	}

	/**
	 * Erstellt einen neuen Knoten
	 *
	 * @param integer $parent_id
	 * 	ID der übergeordneten Seite
	 * @param array $insert_values
	 * @return boolean
	 */
	function insertNode($parent_id, array $insert_values) {
		// Keine übergeordnete Seite zugewiesen
		if (ACP3_Validate::isNumber($parent_id) === false || ACP3_CMS::$db->countRows('*', $this->table_name, 'id = ' . $parent_id) == 0) {
			ACP3_CMS::$db->link->beginTransaction();

			// Letzten Eintrag selektieren
			if ($this->enable_blocks === true)
				$node = ACP3_CMS::$db->select('MAX(right_id) AS right_id', $this->table_name, 'block_id = ' . ACP3_CMS::$db->escape($insert_values['block_id']));
			if ($this->enable_blocks === false || empty($node[0]['right_id'])) {
				$node = ACP3_CMS::$db->select('MAX(right_id) AS right_id', $this->table_name);
			}

			// left_id und right_id Werte für das Anhängen entsprechend erhöhen
			$insert_values['left_id'] = !empty($node[0]['right_id']) ? $node[0]['right_id'] + 1 : 1;
			$insert_values['right_id'] = !empty($node[0]['right_id']) ? $node[0]['right_id'] + 2 : 2;

			$bool = ACP3_CMS::$db->insert($this->table_name, $insert_values);
			$root_id = ACP3_CMS::$db->link->lastInsertId();

			$bool2 = ACP3_CMS::$db->update($this->table_name, array('root_id' => $root_id), 'id = ' . $root_id);
			$bool3 = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $insert_values['left_id'], 0);

			ACP3_CMS::$db->link->commit();

			return $bool !== null && $bool2 !== null && $bool3 !== null ? true : false;
		// Übergeordnete Seite zugewiesen
		} else {
			$parent = ACP3_CMS::$db->select('root_id, left_id, right_id', $this->table_name, 'id = ' . $parent_id);

			ACP3_CMS::$db->link->beginTransaction();

			// Alle nachfolgenden Menüeinträge anpassen
			ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + 2, right_id = right_id + 2 WHERE left_id > ' . $parent[0]['right_id'], 0);
			// Übergeordnete Menüpunkte anpassen
			ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id + 2 WHERE root_id = ' . $parent[0]['root_id'] . ' AND left_id <= ' . $parent[0]['left_id'] . ' AND right_id >= ' . $parent[0]['right_id'], 0);

			ACP3_CMS::$db->link->commit();

			$insert_values['root_id'] = $parent[0]['root_id'];
			$insert_values['left_id'] = $parent[0]['right_id'];
			$insert_values['right_id'] = $parent[0]['right_id'] + 1;

			return ACP3_CMS::$db->insert($this->table_name, $insert_values);
		}
	}

	/**
	 * Methode zum Bearbeiten eines Knotens
	 *
	 * @param integer $id
	 * 	ID des zu bearbeitenden Knotens
	 * @param integer $parent
	 * 	ID des neuen Elternelements
	 * @param integer $block_id
	 * 	ID des neuen Blocks
	 * @param array $update_values
	 * @return boolean
	 */
	function editNode($id, $parent, $block_id, array $update_values) {
		if (ACP3_Validate::isNumber($id) === true && (ACP3_Validate::isNumber($parent) === true || $parent == '') && ACP3_Validate::isNumber($block_id) === true) {
			// Die aktuelle Seite mit allen untergeordneten Seiten selektieren
			$items = ACP3_CMS::$db->query('SELECT n.id, n.root_id, n.left_id, n.right_id' . ($this->enable_blocks === true ? ', n.block_id' : '') . ' FROM {pre}' . $this->table_name . ' AS p, {pre}' . $this->table_name . ' AS n WHERE p.id = ' . $id . ' AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC');

			// Überprüfen, ob Seite ein Root-Element ist und ob dies auch so bleiben soll
			if (empty($parent) &&
					($this->enable_blocks === false || ($this->enable_blocks === true && $block_id == $items[0]['block_id'])) &&
					ACP3_CMS::$db->countRows('*', $this->table_name, 'left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id']) == 0) {
				$bool = ACP3_CMS::$db->update($this->table_name, $update_values, 'id = ' . $id);
			} else {
				// Überprüfung, falls Seite kein Root-Element ist, aber keine Veränderung vorgenommen werden soll...
				$chk_parent = ACP3_CMS::$db->query('SELECT id FROM {pre}' . $this->table_name . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'] . ' ORDER BY left_id DESC LIMIT 1');
				if (isset($chk_parent[0]) && $chk_parent[0]['id'] == $parent) {
					$bool = ACP3_CMS::$db->update($this->table_name, $update_values, 'id = ' . $id);
				// ...ansonsten den Baum bearbeiten...
				} else {
					$bool = false;
					// Differenz zwischen linken und rechten Wert bilden
					$page_diff = $items[0]['right_id'] - $items[0]['left_id'] + 1;

					// Neues Elternelement
					$new_parent = ACP3_CMS::$db->select('root_id, left_id, right_id', $this->table_name, 'id = ' . $parent);

					// Knoten werden eigenes Root-Element
					if (empty($new_parent)) {
						$root_id = $id;
						if ($this->enable_blocks === true) {
							// Knoten in anderen Block verschieben
							if ($items[0]['block_id'] != $block_id) {
								$new_block = ACP3_CMS::$db->select('MIN(left_id) AS left_id', $this->table_name, 'block_id = ' . $block_id);
								// Falls die Knoten in einen leeren Block verschoben werden sollen,
								// die right_id des letzten Elementes verwenden
								if (empty($new_block) || is_null($new_block[0]['left_id']) === true) {
									$new_block = ACP3_CMS::$db->select('MAX(right_id) AS left_id', $this->table_name);
									$new_block[0]['left_id']+= 1;
								}

								if ($block_id > $items[0]['block_id'])
									$new_block[0]['left_id']-= $page_diff;

								$diff = $new_block[0]['left_id'] - $items[0]['left_id'];

								ACP3_CMS::$db->link->beginTransaction();
								ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
								ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'], 0);
								ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + ' . $page_diff . ', right_id = right_id + ' . $page_diff . ' WHERE left_id >= ' . $new_block[0]['left_id'], 0);
							// Element zum neuen Wurzelknoten machen
							} else {
								$max_id = ACP3_CMS::$db->select('MAX(right_id) AS right_id', $this->table_name, 'block_id = ' . $items[0]['block_id']);
								$diff = $max_id[0]['right_id'] - $items[0]['right_id'];

								ACP3_CMS::$db->link->beginTransaction();
								ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
								ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'] . ' AND block_id = ' . $items[0]['block_id'], 0);
							}
						} else {
							$max_id = ACP3_CMS::$db->select('MAX(right_id) AS right_id', 'acl_roles');
							$diff = $max_id[0]['right_id'] - $items[0]['right_id'];

							ACP3_CMS::$db->link->beginTransaction();
							ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
							ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'], 0);
						}
					// Knoten werden Kinder von einem anderen Knoten
					} else {
						// Teilbaum nach unten...
						if ($new_parent[0]['left_id'] > $items[0]['left_id']) {
							$new_parent[0]['left_id']-= $page_diff;
							$new_parent[0]['right_id']-= $page_diff;
						}

						$diff = $new_parent[0]['left_id'] - $items[0]['left_id'] + 1;
						$root_id = $new_parent[0]['root_id'];

						ACP3_CMS::$db->link->beginTransaction();
						ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id - ' . $page_diff . ' WHERE left_id < ' . $items[0]['left_id'] . ' AND right_id > ' . $items[0]['right_id'], 0);
						ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $page_diff . ', right_id = right_id - ' . $page_diff . ' WHERE left_id > ' . $items[0]['right_id'], 0);
						ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET right_id = right_id + ' . $page_diff . ' WHERE left_id <= ' . $new_parent[0]['left_id'] . ' AND right_id >= ' . $new_parent[0]['right_id'], 0);
						ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + ' . $page_diff . ', right_id = right_id + ' . $page_diff . ' WHERE left_id > ' . $new_parent[0]['left_id'], 0);
					}

					// Einträge aktualisieren
					$c_items = count($items);
					for ($i = 0; $i < $c_items; ++$i) {
						$parent = ACP3_CMS::$db->query('SELECT id FROM {pre}' . $this->table_name . ' WHERE left_id < ' . ($items[$i]['left_id'] + $diff) . ' AND right_id > ' . ($items[$i]['right_id'] + $diff) . ' ORDER BY left_id DESC LIMIT 1');
						$bool = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET ' . ($this->enable_blocks === true ? 'block_id = ' . $block_id . ', ' : '') . 'root_id = ' . $root_id . ', parent_id = ' . (!empty($parent[0]['id']) ? $parent[0]['id'] : 0) . ', left_id = ' . ($items[$i]['left_id'] + $diff) . ', right_id = ' . ($items[$i]['right_id'] + $diff) . ' WHERE id = ' . $items[$i]['id'], 0);
						if ($bool === false)
							break;
					}
					ACP3_CMS::$db->update($this->table_name, $update_values, 'id = ' . $id);
					ACP3_CMS::$db->link->commit();
				}
			}
			return $bool;
		}
		return false;
	}

	/**
	 * Methode zum Umsortieren von Knoten
	 *
	 * @param integer $id
	 * @param string $mode
	 * @return boolean
	 */
	public function order($id, $mode) {
		if (ACP3_Validate::isNumber($id) === true && ACP3_CMS::$db->countRows('*', $this->table_name, 'id = ' . $id) == 1) {
			$items = ACP3_CMS::$db->query('SELECT c.id, ' . ($this->enable_block === true ? 'c.block_id, ' : '') . 'c.left_id, c.right_id FROM {pre}' . $this->table_name . ' AS p, {pre}' . $this->table_name . ' AS c WHERE p.id = ' . $id . ' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');

			if ($mode === 'up' && ACP3_CMS::$db->countRows('*', $this->table_name, 'right_id = ' . ($items[0]['left_id'] - 1) . ($this->enable_block === true ? ' AND block_id = ' . $items[0]['block_id'] : '')) > 0) {
				// Vorherigen Knoten mit allen Kindern selektieren
				$elem = ACP3_CMS::$db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}' . $this->table_name . ' AS p, {pre}' . $this->table_name . ' AS c WHERE p.right_id = ' . ($items[0]['left_id'] - 1) . ' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');
				$diff_left = $items[0]['left_id'] - $elem[0]['left_id'];
				$diff_right = $items[0]['right_id'] - $elem[0]['right_id'];
			} elseif ($mode === 'down' && ACP3_CMS::$db->countRows('*', $this->table_name, 'left_id = ' . ($items[0]['right_id'] + 1) . ($this->enable_block === true ? ' AND block_id = ' . $items[0]['block_id'] : '')) > 0) {
				// Nachfolgenden Knoten mit allen Kindern selektieren
				$elem = ACP3_CMS::$db->query('SELECT c.id, c.left_id, c.right_id FROM {pre}' . $this->table_name . ' AS p, {pre}' . $this->table_name . ' AS c WHERE p.left_id = ' . ($items[0]['right_id'] + 1) . ' AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC');
				$diff_left = $elem[0]['left_id'] - $items[0]['left_id'];
				$diff_right = $elem[0]['right_id'] - $items[0]['right_id'];
			} else {
				return false;
			}

			$c_elem = count($elem);
			$c_pages = count($items);
			$elem_ids = $pages_ids = '';

			for ($i = 0; $i < $c_elem; ++$i) {
				$elem_ids.= 'id = ' . $elem[$i]['id'] . ' OR ';
			}
			for ($i = 0; $i < $c_pages; ++$i) {
				$pages_ids.= 'id = ' . $items[$i]['id'] . ' OR ';
			}

			ACP3_CMS::$db->link->beginTransaction();

			if ($mode === 'up') {
				$bool = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + ' . $diff_right . ', right_id = right_id + ' . $diff_right . ' WHERE ' . substr($elem_ids, 0, -4), 0);
				$bool2 = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $diff_left . ', right_id = right_id - ' . $diff_left . ' WHERE ' . substr($pages_ids, 0, -4), 0);
			} else {
				$bool = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id - ' . $diff_left . ', right_id = right_id - ' . $diff_left . ' WHERE ' . substr($elem_ids, 0, -4), 0);
				$bool2 = ACP3_CMS::$db->query('UPDATE {pre}' . $this->table_name . ' SET left_id = left_id + ' . $diff_right . ', right_id = right_id + ' . $diff_right . ' WHERE ' . substr($pages_ids, 0, -4), 0);
			}

			ACP3_CMS::$db->link->commit();

			return $bool && $bool2;
		}
		return false;
	}
}