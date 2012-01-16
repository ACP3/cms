<?php
/**
 * Date
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Stellt Funktionen zur Datumsformatierung und Ausrichtung an den Zeitzonen bereit
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class date
{
	/**
	 * Zeitverschiebung von Greenwich mit eventueller Sommerzeit
	 *
	 * @var integer
	 * @access private
	 */
	private $offset_dst = 0;
	/**
	 * Zeitverschiebung von Greenwich ohne Sommerzeit
	 *
	 * @var integer
	 * @access private
	 */
	private $offset_real = 0;
	/**
	 * Sommerzeit an/aus
	 *
	 * @var integer
	 * @access private
	 */
	private $dst = 0;
	/**
	 * Lanes Datumsformat
	 */
	private $date_format_long = CONFIG_DATE_FORMAT_LONG;
	/**
	 * Kurzes Datumsformat
	 */
	private $date_format_short = CONFIG_DATE_FORMAT_SHORT;

	/**
	 * Falls man sich als User authentifiziert hat, eingestellte Zeitzone + Sommerzeiteinstellung holen
	 *
	 */
	function __construct()
	{
		global $auth;
		$info = $auth->getUserInfo();

		if (!empty($info)) {
			$this->dst = (int) $info['dst'];
			$this->date_format_long = $info['date_format_long'];
			$this->date_format_short = $info['date_format_short'];
			$time_zone = (int) $info['time_zone'];
		} else {
			$this->dst = CONFIG_DATE_DST;
			$time_zone = CONFIG_DATE_TIME_ZONE;
		}

		$this->offset_real = $time_zone;
		$this->offset_dst = $time_zone + ($this->dst == 1 ? 3600 : 0);
	}
	/**
	 * Zeigt Dropdown-Menüs für die Veröffentlichungsdauer von Inhalten an
	 *
	 * @param mixed $name
	 * 	Name des jeweiligen Inputfeldes
	 * @param mixed $value
	 * 	Der Zeitstempel des jeweiligen Eintrages
	 * @param string $format
	 *	Das anzuzeigende Format im Textfeld
	 * @param array $params
	 *	Dient dem Festlegen von weiteren Parametern
	 * @param integer $range
	 *	1 = Start- und Enddatum anzeigen
	 *	2 = Einfaches Inputfeld mitsamt Datepicker anzeigen
	 * @return string
	 */
	public function datepicker($name, $value = '', $format = 'Y-m-d H:i', array $params = array(), $range = 1, $mode = 1)
	{
		global $tpl;

		$datepicker = array(
			'range' => is_array($name) && $range === 1 ? 1 : 0,
			'params' => array(
				'firstDay' => '\'1\'',
				'dateFormat' => '\'yy-mm-dd\'',
				'showOn' => '\'button\'',
				'buttonImage' => '\'' . ROOT_DIR . 'images/crystal/16/cal.png\'',
				'buttonImageOnly' => 'true',
				'constrainInput' => 'false',
			)
		);

		// Zusätzliche Datepicker-Parameter hinzufügen
		if (!empty($params) && is_array($params)) {
			$datepicker['params'] = array_merge($datepicker['params'], $params);
		}

		// Veröffentlichungszeitraum
		if (is_array($name) && $range === 1) {
			if (!empty($_POST['form'][$name[0]]) && !empty($_POST['form'][$name[1]])) {
				$value_start = $_POST['form'][$name[0]];
				$value_end = $_POST['form'][$name[1]];
			} elseif (is_array($value) && validate::isNumber($value[0]) && validate::isNumber($value[1])) {
				$value_start = $this->format($value[0], $format, $mode);
				$value_end = $this->format($value[1], $format, $mode);
			} else {
				$value_start = $this->format(time(), $format, $mode);
				$value_end = $this->format(time(), $format, $mode);
			}

			$datepicker['name_start'] = $name[0];
			$datepicker['name_end'] = $name[1];
			$datepicker['value_start'] = $value_start;
			$datepicker['value_end'] = $value_end;
		// Einfaches Inputfeld mit Datepicker
		} else {
			if (!empty($_POST['form'][$name])) {
				$value = $_POST['form'][$name];
			} elseif (validate::isNumber($value)) {
				$value = $this->format($value, $format, $mode);
			} else {
				$value = $this->format(time(), $format, $mode);
			}

			$datepicker['name'] = $name;
			$datepicker['value'] = $value;
		}

		$tpl->assign('datepicker', $datepicker);

		return modules::fetchTemplate('common/date.tpl');
	}
	/**
	 * Gibt ein formatiertes Datum zurück
	 *
	 * @param integer $time_stamp
	 * @param string $format
	 * @param integer $mode
	 *	1 = Sommerzeit beachten
	 *	2 = Sommerzeit nicht beachten
	 * @return string
	 */
	public function format($time_stamp, $format = 'long', $mode = 1)
	{
		// Datum in gewünschter Formatierung ausgeben
		switch ($format) {
			case 'long':
				$format = $this->date_format_long;
				break;
			case 'short':
				$format = $this->date_format_short;
				break;
		}
		return gmdate($format, $time_stamp + ($mode === 1 ? $this->offset_dst : $this->offset_real));
	}
	/**
	 * Gibt die Formularfelder für den Veröffentlichungszeitraum aus
	 *
	 * @param integer $start
	 * @param integer $end
	 * @param string $format
	 * @return string
	 */
	public function period($start, $end, $format = 'long')
	{
		if (validate::isNumber($start) && validate::isNumber($end)) {
			global $lang;
			if ($start >= $end) {
				return sprintf($lang->t('common', 'since_date'), $this->format($start, $format));
			} else {
				return sprintf($lang->t('common', 'from_start_to_end'), $this->format($start, $format), $this->format($end, $format));
			}
		}
		return '';
	}
	/**
	 * Gibt einen einfachen Zeitstempel zurück, welcher sich an UTC ausrichtet
	 *
	 * @param string $value
	 * @return integer
	 */
	public function timestamp($value = 0)
	{
		// Zeitstempel aus Veröffentlichungszeitraum heraus generieren
		if (!empty($value) && validate::date($value)) {
			$value = strtotime($value);

			$offset = (date('I', $value) == 1) ? -3600 : 0;

			return gmdate('U', $value + $offset - $this->offset_dst);
		}
		return gmdate('U');
	}
}