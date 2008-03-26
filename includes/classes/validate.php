<?php
/**
 * Validate
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Klasse zur Validierung von bestimmten Einträgen
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class validate
{
	/**
	 * Überpürft, ob der eingegebene Captcha mit dem generierten übereinstimmt
	 *
	 * @param string $input
	 * @param string $hash
	 * @return boolean
	 */
	public function captcha($input, $hash)
	{
		if (preg_match('/^[a-zA-Z0-9]+$/', $input) && strlen($hash) == 32 && preg_match('/^[a-z0-9]+$/', $hash)) {
			$path = ACP3_ROOT . 'modules/captcha/generated/' . $hash . strtolower($input);
			if (file_exists($path)) {
				@unlink($path);
				return true;
			}
		}
		return false;
	}
	/**
	 * Überprüft, ob alle Daten ein sinnvolles Datum ergeben
	 *
	 * @param array $values
	 *  Zu überprüfende Werte
	 * @param string $prefix_start
	 *  Präfix für das Veröffentlichungsdatum
	 * @param string $prefix_end
	 *  Präfix für das Enddatum
	 * @return boolean
	 */
	public function date($values, $prefix_start = 'start', $prefix_end = 'end')
	{
		if (!$this->isNumber($values[$prefix_start . '_day']) ||
			!$this->isNumber($values[$prefix_start . '_month']) ||
			!$this->isNumber($values[$prefix_start . '_year']) ||
			!$this->isNumber($values[$prefix_start . '_hour']) ||
			!$this->isNumber($values[$prefix_start . '_min']) ||
			!$this->isNumber($values[$prefix_end . '_day']) ||
			!$this->isNumber($values[$prefix_end . '_month']) ||
			!$this->isNumber($values[$prefix_end . '_year']) ||
			!$this->isNumber($values[$prefix_end . '_hour']) ||
			!$this->isNumber($values[$prefix_end . '_min']) ||
			mktime($values[$prefix_start . '_hour'], $values[$prefix_start . '_min'], 0, $values[$prefix_start . '_month'], $values[$prefix_start . '_day'], $values[$prefix_start . '_year']) >
			mktime($values[$prefix_end . '_hour'], $values[$prefix_end . '_min'], 0, $values[$prefix_end . '_month'], $values[$prefix_end . '_day'], $values[$prefix_end . '_year'])) {
			return false;
		}
		return true;
	}
	/**
	 * Überprüft, ob eine Standardkonforme E-Mail-Adresse übergeben wurde
	 *
	 * @copyright HTML/QuickForm/Rule/Email.php
	 * 	Suchmuster von PEAR entnommen
	 * @param string $var
	 *  Zu überprüfende E-Mail-Adresse
	 * @return boolean
	 */
	public function email($var)
	{
		$pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

		return preg_match($pattern, $var);
	}
	/**
	 * Überprüft eine Variable, ob diese nur aus Ziffern besteht
	 *
	 * @param mixed $var
	 * @return boolean
	 */
	public function isNumber($var)
	{
		return preg_match('/^(\d+)$/', $var);
	}
	/**
	 * Überprüfen, ob es ein unterstütztes Bildformat ist
	 *
	 * @param string $var
	 *  Zu überprüfendes Bild
	 * @return boolean
	 */
	public function isPicture($var)
	{
		$info = getimagesize($var);

		return $info[2] == '1' || $info[2] == '2' || $info[2] == '3' ? true : false;
	}
}
?>