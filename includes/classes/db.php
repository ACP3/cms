<?php
/**
 * Database
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Diese Klasse ist für die Datenbankabfragen zuständig
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class db
{
	/**
	 * Verbindungskennung zur Datenbank
	 *
	 * @var resource
	 * @access public
	 */
	public $link = null;

	/**
	 * Stellt die Verbindung mit der Datenbank her
	 */
	public function connect($db_host, $db_name, $db_user, $db_pwd)
	{
		try {
			$this->link = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pwd);
			$this->link->setAttribute(PDO::ATTR_ERRMODE, defined('DEBUG') && DEBUG ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT);
			$this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			return true;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}
	/**
	 * Beendet die Verbindung zur Datenbank
	 */
	function __destruct()
	{
		$this->link = null;
	}
	/**
	 * Maskiert die Variablen vor dem Eintragen in die Datenbank
	 *
	 * @param mixed $value
	 * 	Zu maskierende Variable
	 * @param integer $mode
	 *	1 = Variablen komplett maskieren
	 * 	2 = Nur Slashes hinzufügen (wichtig für die WYSIWYG-Editoren Eingaben)
	 * 	3 = Slashes entfernen
	 * @return string
	 */
	public function escape($value, $mode = 1)
	{
		$value = trim($value);

		if ($mode == 1) {
			return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		} elseif ($mode == 2) {
			return addslashes($value);
		} else {
			return stripslashes($value);
		}
	}
	/**
	 * Führt die SQL Abfragen durch
	 *
	 * @param string $query
	 * 	Die durchzuführende SQL Abfrage
	 * @param integer $mode
	 * 	1 = Nur Datensätze zählen
	 *  2 = Datensätze als assoziatives Array ausgeben
	 *  3 = Rückgabewert der SQL Abfrage
	 * @return mixed
	 */
	public function query($query, $mode = 2)
	{
		try {
			switch ($mode) {
				// Anzahl der Zeilen zählen
				case 1:
					$stmt = $this->link->query($query);
					$result = $stmt->fetchColumn();
					break;
				// Query ausführen, die ein Resultset zurückgibt
				case 2:
					$stmt = $this->link->query($query);
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					break;
				// Queries ohne Resultset
				default:
					$result = $this->link->query($query);
			}
			return $result;
		} catch(PDOException $e) {
			echo $e->getMessage();
			return null;
		}
	}
	/**
	 * Führt den DELETE Befehl aus
	 *
	 * @param string $table
	 *  Die betroffene Tabelle der Datenbank
	 * @param string $field
	 *  Die betroffenen Felder der Tabelle
	 * @param integer $limit
	 *  Die maximal zu löschenden Einträge, falls mehr als ein Eintrag gelöscht werden könnte
	 * @return boolean
	 */
	public function delete($table, $field, $limit = 0)
	{
		$query = 'DELETE FROM ' . CONFIG_DB_PRE . $table . ' WHERE ' . $field;
		$query.= !empty($limit) ? ' LIMIT ' . $limit : '';

		return $this->query($query, 0);
	}
	/**
	 * Führt den INSERT Befehl aus
	 *
	 * @param string $table
	 * 	Die betroffene Tabelle der Datenbank
	 * @param array $insert_values
	 *  Erwartet ein Array mit den betroffenen Feldern als Schlüssel und dazugehörigem Inhalt
	 * @return boolean
	 */
	public function insert($table, $insert_values)
	{
		if (!empty($insert_values) && is_array($insert_values)) {
			$fields = '';
			$values = '';
			foreach ($insert_values as $field => $value) {
				$fields.= $field . ', ';
				$values.= '\'' . $value . '\', ';
			}

			$query = 'INSERT INTO ' . CONFIG_DB_PRE . $table . ' (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ')';

			return $this->query($query, 0);
		}
		return false;
	}
	/**
	 * Führt die SELECT Abfrage durch
	 *
	 * @param string $field
	 * 	Selektiert der Felder
	 * @param string $table
	 * 	Die betroffene Tabelle der Datenbank
	 * @param string $where
	 * 	WHERE Bedingung der SQL Abfrage
	 * @param string $order
	 * 	ORDER BY Bedingung der SQL Abfrage
	 * @param integer $min
	 * 	Erster Parameter der LIMIT Bedingung der SQL Abfrage
	 * @param integer $max
	 * 	Zweiter Parameter der LIMIT Bedingung der SQL Abfrage
	 * @param integer $mode
	 * 	@see query()
	 * @return @see query()
	 */
	public function select($field, $table, $where = 0, $order = 0, $min = '', $max = '', $mode = 2)
	{
		$field = empty($field) ? '*' : $field;
		$query = 'SELECT ' . $field . ' FROM ' . CONFIG_DB_PRE . $table;
		$query.= empty($where) ? '' : ' WHERE ' . $where;
		$query.= empty($order) ? '' : ' ORDER BY ' . $order;
		if ($min != '' && $max == '') {
			$query.= ' LIMIT ' . $min;
		} elseif ($min != '' && $max != '') {
			$query.= ' LIMIT ' . $min . ',' . $max;
		}

		return $this->query($query, $mode);
	}
	/**
	 * Gibt die Anzahl der Datensätze zurück
	 *
	 * @param string $field
	 *  Das Feld, anhand welchem gezählt werden soll
	 * @param string $table
	 * 	Die betroffene Tabelle der Datenbank
	 * @param string $where
	 * 	WHERE Bedingung der SQL Abfrage
	 * @return @see query()
	 */
	public function countRows($field, $table, $where = 0)
	{
		$query = 'SELECT COUNT(' . $field . ') FROM ' . CONFIG_DB_PRE . $table;
		$query.= empty($where) ? '' : ' WHERE ' . $where;

		return $this->query($query, 1);
	}
	/**
	 * Führt den UPDATE Befehl aus
	 *
	 * @param string $table
	 *  Die betroffene Tabelle der Datenbank
	 * @param array $update_values
	 *  Erwartet ein Array mit den betroffenen Feldern als Schlüssel und dazugehörigem Inhalt
	 * @param string $where
	 *  WHERE Bedingung der SQL Abfrage
	 * @return boolean
	 */
	public function update($table, $update_values, $where = 0, $limit = 0)
	{
		if (!empty($update_values) && is_array($update_values)) {
			$set = '';
			foreach ($update_values as $field => $value) {
				$set.= $field . ' = \'' . $value . '\', ';
			}

			$query = 'UPDATE ' . CONFIG_DB_PRE . $table . ' SET ' . substr($set, 0, -2);
			$query.= !empty($where) ? ' WHERE ' . $where : '';
			$query.= !empty($limit) ? ' LIMIT ' . $limit : '';

			return $this->query($query, 0);
		}
		return false;
	}
}
?>