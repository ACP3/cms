<?php
/**
 * Date
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
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
	 * Zeitverschiebung
	 *
	 * @var integer
	 * @access private
	 */
	private $offset = 0;
	
	/**
	 * Falls man sich als User authentifiziert hat, eingestellte Zeitzone + Sommerzeiteinstellung holen
	 *
	 */
	function __construct()
	{
		global $auth;
		$info = $auth->getUserInfo();

		if (!empty($info)) {
			$dst = $info['dst'];
			$time_zone = $info['time_zone'];
		} else {
			$dst = CONFIG_DST;
			$time_zone = CONFIG_TIME_ZONE;
		}
		$this->offset = $time_zone + ($dst == '1' ? 3600 : 0);
	}
	/**
	 * Gibt ein formatiertes Datum zurück
	 *
	 * @param mixed $time_stamp
	 * @param string $format
	 * @return integer
	 */
	public function format($time_stamp, $format = 0)
	{
		// Datum in gewünschter Formatierung ausgeben
		$format = !empty($format) ? $format : CONFIG_DATE;
		return gmdate($format, $time_stamp + $this->offset);
	}
	public function period($start, $end)
	{
		if (validate::isNumber($start) && validate::isNumber($end)) {
			global $lang;
			if ($start >= $end) {
				return sprintf($lang->t('common', 'since_date'), $this->format($start));
			} else {
				return sprintf($lang->t('common', 'from_start_to_end'), $this->format($start), $this->format($end));
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
			return strtotime($value, $this->timestamp());
		}
		return gmdate('U', time());
	}
}
?>