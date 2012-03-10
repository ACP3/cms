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
class ACP3_Date
{
	/**
	 * Langes Datumsformat
	 * 
	 * @var string
	 */
	private $date_format_long = CONFIG_DATE_FORMAT_LONG;
	/**
	 * Kurzes Datumsformat
	 * 
	 * @var string
	 */
	private $date_format_short = CONFIG_DATE_FORMAT_SHORT;
	/**
	 * PHP DateTimeZone-Object
	 *
	 * @var object 
	 */
	private $date_time_zone = null;

	/**
	 * Falls man sich als User authentifiziert hat, eingestellte Zeitzone + Sommerzeiteinstellung holen
	 *
	 */
	function __construct()
	{
		global $auth;
		$info = $auth->getUserInfo();

		if (!empty($info)) {
			$this->date_format_long = $info['date_format_long'];
			$this->date_format_short = $info['date_format_short'];
			$time_zone = $info['time_zone'];
		} else {
			$time_zone = CONFIG_DATE_TIME_ZONE;
		}
		$this->date_time_zone = new DateTimeZone($time_zone);
	}
	/**
	 * Gibts ein Array mit den möglichen Datumsformaten aus,
	 * um diese als Dropdownmenü darstellen zu können
	 *
	 * @param string $format
	 *	Optionaler Parameter für das aktuelle Datumsformat
	 * @return array 
	 */
	public function dateformatDropdown($format = '')
	{
		global $lang;

		$dateformat = array();
		$dateformat[0]['value'] = 'short';
		$dateformat[0]['selected'] = selectEntry('dateformat', 'short', $format);
		$dateformat[0]['lang'] = $lang->t('common', 'date_format_short');
		$dateformat[1]['value'] = 'long';
		$dateformat[1]['selected'] = selectEntry('dateformat', 'long', $format);
		$dateformat[1]['lang'] = $lang->t('common', 'date_format_long');

		return $dateformat;
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
			'range' => is_array($name) === true && $range === 1 ? 1 : 0,
			'params' => array(
				'firstDay' => '\'1\'',
				'dateFormat' => '\'yy-mm-dd\'',
				'showOn' => '\'button\'',
				'buttonImage' => '\'' . DESIGN_PATH . 'images/16/cal.png\'',
				'buttonImageOnly' => 'true',
				'constrainInput' => 'false',
			)
		);

		// Zusätzliche Datepicker-Parameter hinzufügen
		if (!empty($params) && is_array($params) === true) {
			$datepicker['params'] = array_merge($datepicker['params'], $params);
		}

		// Veröffentlichungszeitraum
		if (is_array($name) === true && $range === 1) {
			if (!empty($_POST['form'][$name[0]]) && !empty($_POST['form'][$name[1]])) {
				$value_start = $_POST['form'][$name[0]];
				$value_end = $_POST['form'][$name[1]];
			} elseif (is_array($value) === true && ACP3_Validate::isNumber($value[0]) === true && ACP3_Validate::isNumber($value[1]) === true) {
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
			} elseif (ACP3_Validate::isNumber($value) === true) {
				$value = $this->format($value, $format, $mode);
			} else {
				$value = $this->format(time(), $format, $mode);
			}

			$datepicker['name'] = $name;
			$datepicker['value'] = $value;
		}

		$tpl->assign('datepicker', $datepicker);

		return ACP3_View::fetchTemplate('common/date.tpl');
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
	public function format($time_stamp, $format = 'long')
	{
		global $lang;

		// Datum in gewünschter Formatierung ausgeben
		switch ($format) {
			case 'long':
				$format = $this->date_format_long;
				break;
			case 'short':
				$format = $this->date_format_short;
				break;
		}

		// Wochen- und Monatstage lokalisieren
		$replace = array();
		if (strpos($format, 'D') !== false) {
			$replace = array(
				'Mon' => $lang->t('common', 'date_mon'),
				'Tue' => $lang->t('common', 'date_tue'),
				'Wed' => $lang->t('common', 'date_wed'),
				'Thu' => $lang->t('common', 'date_thu'),
				'Fri' => $lang->t('common', 'date_fri'),
				'Sat' => $lang->t('common', 'date_sat'),
				'Sun' => $lang->t('common', 'date_sun')
			);
		} elseif (strpos($format, 'l') !== false) {
			$replace = array(
				'Monday' => $lang->t('common', 'date_monday'),
				'Tuesday' => $lang->t('common', 'date_tuesday'),
				'Wednesday' => $lang->t('common', 'date_wednesday'),
				'Thursday' => $lang->t('common', 'date_thursday'),
				'Friday' => $lang->t('common', 'date_friday'),
				'Saturday' => $lang->t('common', 'date_saturday'),
				'Sunday' => $lang->t('common', 'date_sunday')
			);
		}
		if (strpos($format, 'M') !== false) {
			$replace = array_merge($replace, array(
				'Jan' => $lang->t('common', 'date_jan'),
				'Feb' => $lang->t('common', 'date_feb'),
				'Mar' => $lang->t('common', 'date_mar'),
				'Apr' => $lang->t('common', 'date_apr'),
				'May' => $lang->t('common', 'date_may_abbr'),
				'Jun' => $lang->t('common', 'date_jun'),
				'Jul' => $lang->t('common', 'date_jul'),
				'Aug' => $lang->t('common', 'date_aug'),
				'Sep' => $lang->t('common', 'date_sep'),
				'Oct' => $lang->t('common', 'date_oct'),
				'Nov' => $lang->t('common', 'date_nov'),
				'Dec' => $lang->t('common', 'date_dec')
			));
		} elseif (strpos($format, 'F') !== false) {
			$replace = array_merge($replace, array(
				'January' => $lang->t('common', 'date_january'),
				'February' => $lang->t('common', 'date_february'),
				'March' => $lang->t('common', 'date_march'),
				'April' => $lang->t('common', 'date_april'),
				'May' => $lang->t('common', 'date_may_full'),
				'June' => $lang->t('common', 'date_june'),
				'July' => $lang->t('common', 'date_july'),
				'August' => $lang->t('common', 'date_august'),
				'September' => $lang->t('common', 'date_september'),
				'October' => $lang->t('common', 'date_october'),
				'November' => $lang->t('common', 'date_november'),
				'December' => $lang->t('common', 'date_december')
			));
		}

		$date_time = new DateTime('', $this->date_time_zone);
		$date_time->setTimestamp($time_stamp);
		return strtr($date_time->format($format), $replace);
	}
	/**
	 * Liefert ein Array mit allen Zeitzonen dieser Welt aus
	 *
	 * @param string $current_value
	 * @return array
	 */
	public function getTimeZones($current_value = '')
	{
		$timeZones = array(
			'Africa' => DateTimeZone::listIdentifiers(DateTimeZone::AFRICA),
			'America' => DateTimeZone::listIdentifiers(DateTimeZone::AMERICA),
			'Antarctica' => DateTimeZone::listIdentifiers(DateTimeZone::ANTARCTICA),
			'Arctic' => DateTimeZone::listIdentifiers(DateTimeZone::ARCTIC),
			'Asia' => DateTimeZone::listIdentifiers(DateTimeZone::ASIA),
			'Atlantic' => DateTimeZone::listIdentifiers(DateTimeZone::ATLANTIC),
			'Australia' => DateTimeZone::listIdentifiers(DateTimeZone::AUSTRALIA),
			'Europe' => DateTimeZone::listIdentifiers(DateTimeZone::EUROPE),
			'Indian' => DateTimeZone::listIdentifiers(DateTimeZone::INDIAN),
			'Pacitic' => DateTimeZone::listIdentifiers(DateTimeZone::PACIFIC),
			'UTC' => DateTimeZone::listIdentifiers(DateTimeZone::UTC),
		);

		foreach ($timeZones as $key => $values) {
			$i = 0;
			foreach ($values as $row) {
				unset($timeZones[$key][$i]);
				$timeZones[$key][$row]['selected'] = selectEntry('date_time_zone', $row, $current_value);
				++$i;
			}
		}
		return $timeZones;
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
		if (ACP3_Validate::isNumber($start) === true && ACP3_Validate::isNumber($end) === true) {
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
		if (!empty($value) && ACP3_Validate::date($value) === true) {
			$date_time = new DateTime($value, $this->date_time_zone);
			return $date_time->format('U');
		}
		$date_time = new DateTime('now', $this->date_time_zone);
		return $date_time->format('U');
	}
}