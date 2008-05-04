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
	public static function captcha($input, $hash)
	{
		if (preg_match('/^[a-zA-Z0-9]+$/', $input) && self::isMD5($hash)) {
			$path = ACP3_ROOT . 'uploads/captcha/' . $hash . strtolower($input);
			if (is_file($path)) {
				@unlink($path);
				return true;
			}
		}
		return false;
	}
	/**
	 * Überprüft, ob alle Daten ein sinnvolles Datum ergeben
	 *
	 * @param array $value
	 *  Der zu überprüfende Wert
	 * @return boolean
	 */
	public static function date($value)
	{
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})( ([01][0-9]|2[0-3]):([0-5][0-9])){0,1}$/', $value, $matches)) {
			if (checkdate($matches[2], $matches[3], $matches[1])) {
				return true;
			}
		}
		return false;
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
	public static function email($var)
	{
		$pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

		return preg_match($pattern, $var);
	}
	/**
	 * Überprüft, ob ein gültiger MD5-Hash übergeben wurde
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isMD5($string)
	{
		return preg_match('/^[a-f0-9]+$/', $string) && strlen($string) == 32 ? true : false;
 	}
	/**
	 * Überprüft eine Variable, ob diese nur aus Ziffern besteht
	 *
	 * @param mixed $var
	 * @return boolean
	 */
	public static function isNumber($var)
	{
		return preg_match('/^(\d+)$/', $var);
	}
	/**
	 * Überprüfen, ob es ein unterstütztes Bildformat ist
	 *
	 * @param string $file
	 *  Zu überprüfendes Bild
	 * @return boolean
	 */
	public static function isPicture($file, $width = '', $height = '', $filesize = '')
	{
		$info = getimagesize($file);

		$isPicture = $info[2] >= '1' && $info[2] <= '3' ? true : false;

		if ($isPicture && self::isNumber($width) && self::isNumber($height) && self::isNumber($filesize)) {
			return $info[0] <= $width && $info[1] <= $height && filesize($file) <= $filesize ? true : false;
		}
		return false;
	}
}
?>