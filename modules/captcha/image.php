<?php
/**
 * Captcha
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit();

if (!empty($modules->gen['hash']) && strlen($modules->gen['hash']) == 32 && preg_match('/^[a-f0-9]+$/', $modules->gen['hash'])) {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Type: image/gif');
	$hash = $modules->gen['hash'];
	$captcha = salt(!empty($modules->gen['length']) && $validate->isNumber($modules->gen['length']) ? $modules->gen['length'] : 5);
	$captchaLength = strlen($captcha);
	$dir = ACP3_ROOT . 'modules/captcha/generated/';

	$im = imagecreate($captchaLength * 25, 30);
	// Hintergrundfarbe
	ImageColorAllocate($im, 255, 255, 255);
	// Textfarbe
	$textColor = ImageColorAllocate($im, 0, 0, 0);
	
	for ($i = 0; $i < $captchaLength; $i++) {
		$textSize = rand(10, 15);
		$angle = rand(0, 30);
		$posLeft = 22 * $i + 10;
		$posTop = rand(20, 25);
		ImageTTFText($im, $textSize, $angle, $posLeft, $posTop, $textColor, 'modules/captcha/DejaVuSans.ttf', $captcha[$i]);
	}
	ImageGif($im, $dir . $hash . strtolower($captcha));
	ImageGif($im);
	ImageDestroy($im);
	
	// Alte Captchas löschen
	$captchas = scandir($dir);
	$c_captchas = count($captchas);
	
	for ($i = 0; $i < $c_captchas; $i++) {
		if (time() - filemtime($dir . $captchas[$i]) > 900) {
			@unlink($dir . $captchas[$i]);
		}
	}
	exit();
} else {
	redirect('errors/404');
}
?>