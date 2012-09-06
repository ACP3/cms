<?php
/**
 * Database
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

/**
 * Diese Klasse ist für die Datenbankabfragen zuständig
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_DB
{
	/**
	 * Verbindungskennung zur Datenbank
	 *
	 * @var resource
	 * @access public
	 */
	public $link = null;

	/**
	 * Tabellenpräfix
	 *
	 * @var string
	 * @access public
	 */
	private $prefix = '';

	/**
	 * Stellt die Verbindung mit der Datenbank her
	 */
	public function connect($db_host, $db_name, $db_user, $db_pwd, $db_pre = '')
	{
		try {
			$this->link = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pwd);
			$this->link->setAttribute(PDO::ATTR_ERRMODE, defined('DEBUG') === true && DEBUG === true ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT);
			$this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			$this->prefix = $db_pre;
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

		if ($mode === 1 || $mode === 2) {
			if ($mode === 1)
				$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
			return substr($this->link->quote($value), 1, -1);
		} else {
			return stripslashes($value);
		}
	}
	/**
	 * Führt die SQL-Abfragen aus
	 *
	 * @param string $query
	 * 	Die durchzuführende SQL-Abfrage
	 * @param integer $mode
	 * 	1 = Nur Datensätze zählen
	 *  2 = Datensätze als assoziatives Array ausgeben
	 *  3 = Rückgabewert der SQL-Abfrage
	 * @return mixed
	 */
	public function query($query, $mode = 2)
	{
		try {
			$query = str_replace('{pre}', $this->prefix, $query);
			switch ($mode) {
				// Anzahl der Zeilen zählen
				case 1:
					$stmt = $this->link->query($query);
					$result = $stmt->fetchColumn();
					break;
				// Query ausführen, die ein Resultset zurückgibt
				case 2:
					$stmt = $this->link->prepare($query);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					break;
				case 3:
					break;
				// Queries ohne Resultset
				default:
					$result = $this->link->query($query);
			}
			return $result;
		} catch(PDOException $e) {
			// SQL-Fehler nur ausgeben, wenn Debugmodus aktiv ist
			if (defined('DEBUG') === true && DEBUG === true) {
				$err = "<strong>There was an error with the query below:</strong><br>\n";
				$err.= $query . "<br><br>\n";
				$err.= '<strong>Error message:</strong><br />' . $e->getMessage() . "<br><br>\n";
				$err.= "<strong>Exiting now...</strong>";
				exit($err);
			}
			return false;
		}
	}

	/**
	 * Führt die SELECT Abfrage durch
	 *
	 * @param string $fields
	 * 	Selektiert der Felder
	 * @param string $table
	 * 	Die betroffene Tabelle der Datenbank
	 * @param string $where
	 * 	WHERE Bedingung der SQL-Abfrage
	 * @param string $order
	 * 	ORDER BY Bedingung der SQL-Abfrage
	 * @param integer $min
	 * 	Erster Parameter der LIMIT Bedingung der SQL-Abfrage
	 * @param integer $max
	 * 	Zweiter Parameter der LIMIT Bedingung der SQL-Abfrage
	 * @param integer $mode
	 * 	@see query()
	 * @return @see query()
	 */
	public function getResults($fields, $table, $where = 0, $order = 0, $min = '', $max = '', $mode = 2)
	{
		$fields = !empty($fields) ? $fields : '*';

		$where = empty($where) ? '' : ' WHERE ' . $where;
		$order = empty($order) ? '' : ' ORDER BY ' . $order;

		if (ACP3_Validate::isNumber($min) && $max == '') {
			$limit = ' LIMIT ' . $min;
		} elseif (ACP3_Validate::isNumber($min) && ACP3_Validate::isNumber($max)) {
			$limit = ' LIMIT ' . $min . ',' . $max;
		} else {
			$limit = '';
		}

		$query = 'SELECT ' . $fields . ' FROM ' . $this->prefix . $table . $where . $order . $limit;

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
	 * 	WHERE Bedingung der SQL-Abfrage
	 * @return @see query()
	 */
	public function countRows($field, $table, $where = 0)
	{
		$where = !empty($where) ? ' WHERE ' . $where : '';

		$query = 'SELECT COUNT(' . $field . ') FROM ' . $this->prefix . $table . $where;

		return $this->query($query, 1);
	}

	/**
	 * Führt den INSERT Befehl aus
	 *
	 * @param string $table
	 * 	Die betroffene Tabelle der Datenbank
	 * @param array $data
	 *  Erwartet ein Array mit den betroffenen Feldern als Schlüssel und dazugehörigem Inhalt
	 * @return boolean
	 */
	public function insert($table, array $data)
	{
		if (!empty($data)) {
			$fields = '';
			$values = '';
			foreach ($data as $field => $value) {
				$fields.= '`' . $field . '`, ';
				$values.= '\'' . $value . '\', ';
			}

			$query = 'INSERT INTO `' . $this->prefix . $table . '` (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ')';

			return $this->query($query, 0);
		}
		return false;
	}

	/**
	 * Führt den UPDATE Befehl aus
	 *
	 * @param string $table
	 *  Die betroffene Tabelle der Datenbank
	 * @param array $data
	 *  Erwartet ein assoziatives Array mit den betroffenen Feldern als Schlüssel und dazugehörigem Inhalt
	 * @param string $where
	 *  WHERE Bedingung der SQL-Abfrage
	 * @return boolean
	 */
	public function update($table, array $data, $where = 0, $limit = 0)
	{
		if (!empty($data)) {
			$fields = '';
			foreach ($data as $field => $value) {
				$fields.= '`' . $field . '` = \'' . $value . '\', ';
			}

			$where = !empty($where) ? ' WHERE ' . $where : '';
			$limit = !empty($limit) && ACP3_Validate::isNumber($limit) ? ' LIMIT ' . $limit : '';

			$query = 'UPDATE `' . $this->prefix . $table . '` SET ' . substr($fields, 0, -2) . $where . $limit;

			return $this->query($query, 0);
		}
		return false;
	}

	/**
	 * Führt den DELETE Befehl aus
	 *
	 * @param string $table
	 *  Die betroffene Tabelle der Datenbank
	 * @param string $where
	 *  Die betroffenen Felder der Tabelle
	 * @param integer $limit
	 *  Die maximal zu löschenden Einträge, falls mehr als ein Eintrag gelöscht werden könnte
	 * @return boolean
	 */
	public function delete($table, $where, $limit = 0)
	{
		$limit = !empty($limit) && ACP3_Validate::isNumber($limit) ? ' LIMIT ' . $limit : '';

		$query = 'DELETE FROM `' . $this->prefix . $table . '` WHERE ' . $where . $limit;

		return $this->query($query, 0);
	}
}