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

if (strlen($uri->hash) == 32 && validate::isMD5($uri->hash)) {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Type: image/gif');
	$hash = $uri->hash;
	$captcha = salt(!empty($uri->length) && validate::isNumber($uri->length) ? $uri->length : 5);
	$captchaLength = strlen($captcha);
	$dir = ACP3_ROOT . 'uploads/captcha/';
	$file = $dir . $hash . strtolower($captcha);

	$im = imagecreate($captchaLength * 25, 30);
	// Hintergrundfarbe
	ImageColorAllocate($im, 255, 255, 255);
	// Textfarbe
	$textColor = ImageColorAllocate($im, 0, 0, 0);
	
	for ($i = 0; $i < $captchaLength; ++$i) {
		$textSize = rand(10, 15);
		$angle = rand(0, 30);
		$posLeft = 22 * $i + 10;
		$posTop = rand(20, 25);
		ImageTTFText($im, $textSize, $angle, $posLeft, $posTop, $textColor, ACP3_ROOT . 'modules/captcha/DejaVuSans.ttf', $captcha[$i]);
	}
	ImageGif($im, $file);
	ImageDestroy($im);
	
	// Alte Captchas löschen
	$captchas = scandir($dir);
	$c_captchas = count($captchas);
	
	for ($i = 0; $i < $c_captchas; ++$i) {
		if (time() - filemtime($dir . $captchas[$i]) > 900) {
			@unlink($dir . $captchas[$i]);
		}
	}
	exit(readfile($file));
} else {
	redirect('errors/404');
}
?>