<?php
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
	 * Verbindungskonnung zur Datenbank
	 *
	 * @var resource
	 * @access public
	 */
	public $con = null;

	/**
	 * Stellt die Verbindung mit der Datenbank her
	 *
	 * @return db
	 */
	function __construct()
	{
		$error = 'Beim Verbinden mit der Datenbank ist folgender Fehler aufgetreten:<br />' . "\nFehler %d - %s";

		switch (CONFIG_DB_TYPE) {
			case 'mysqli':
				$db = @mysqli_connect(CONFIG_DB_HOST, CONFIG_DB_USER, CONFIG_DB_PWD, CONFIG_DB_NAME);
				if (mysqli_connect_errno()) {
					printf($error, mysqli_connect_errno(), mysqli_connect_error());
					exit;
				}
				break;
			default:
				$db = @mysql_connect(CONFIG_DB_HOST, CONFIG_DB_USER, CONFIG_DB_PWD);
				$db_select = @mysql_select_db(CONFIG_DB_NAME, $db);
				if (!$db || !$db_select) {
					printf($error, mysql_errno(), mysql_error());
					exit;
				}
		}
		$this->con = $db;
	}
	/**
	 * Beendet die Verbindung zur Datenbank
	 */
	function __destruct()
	{
		if ($this->con) {
			switch (CONFIG_DB_TYPE) {
				case 'mysqli':
					mysqli_close($this->con);
					break;
				default:
					mysql_close($this->con);
			}
		}
	}
	/**
	 * Falls SQL Fehler auftreten, werden diese ausgegeben
	 */
	function error()
	{
		switch (CONFIG_DB_TYPE) {
			case 'mysqli':
				echo 'Fehler ' . mysqli_errno($this->con) . ' - ' . mysqli_error($this->con);
				break;
			default:
				echo 'Fehler ' . mysql_errno($this->con) . ' - ' . mysql_error($this->con);
		}
	}
	/**
	 * Maskiert die Variablen vor der Eintragung in die Datenbank
	 *
	 * @param mixed $value
	 * 	Zu maskierende Variable
	 * @param integer $mode
	 * 	Maskieren bzw. demaskieren des Inhaltes von $value
	 * @return mixed
	 */
	function escape($value, $mode = 1)
	{
		if ($mode == 1) {
			return htmlspecialchars($value, ENT_QUOTES, CHARSET);
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
	function query($query, $mode = 2)
	{
		$i = 0;
		$new_result = NULL;

		switch (CONFIG_DB_TYPE) {
			case 'mysqli':
				$result = @mysqli_query($this->con, $query);
				if ($result) {
					if ($mode == 1) {
						return @mysqli_num_rows($result);
					} elseif ($mode == 2) {
						while ($data = @mysqli_fetch_assoc($result)) {
							$new_result[$i++] = $data;
						}
						mysqli_free_result($result);
					} else {
						return $result;
					}
				} else {
					$this->error();
				}
				break;
			default:
				$result = @mysql_query($query, $this->con);
				if ($result) {
					if ($mode == 1) {
						return @mysql_num_rows($result);
					} elseif ($mode == 2) {
						while ($data = @mysql_fetch_assoc($result)) {
							$new_result[$i++] = $data;
						}
						mysql_free_result($result);
					} else {
						return $result;
					}
				} else {
					$this->error();
				}
		}
		return $new_result;
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
	function delete($table, $field, $limit = 0)
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
	function insert($table, $insert_values)
	{
		if (is_array($insert_values)) {
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
	function select($field, $table, $where = 0, $order = 0, $min = '', $max = '', $mode = 2)
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
	function update($table, $update_values, $where = 0, $limit = 0)
	{
		if (is_array($update_values)) {
			$set_to = '';
			foreach ($update_values as $field => $value) {
				$set_to.= $field . ' = \'' . $value . '\', ';
			}

			$query = 'UPDATE ' . CONFIG_DB_PRE . $table . ' SET ' . substr($set_to, 0, -2);
			$query.= !empty($where) ? ' WHERE ' . $where : '';
			$query.= !empty($limit) ? ' LIMIT ' . $limit : '';

			return $this->query($query, 0);
		}
		return false;
	}
}
?>