<?php
/**
 * Captcha
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined( 'IN_ACP3' ))
	exit();

if (empty( $modules->gen['hash'] )) {
	redirect( 'errors/404' );
} else {
	header( 'Cache-Control: no-cache, must-revalidate' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Content-Type: image/gif' );
	$hash = base64_decode($db->escape($modules->gen['hash'], 3));
	$hashLength = strlen($hash);
	
	$im = imagecreate( $hashLength * 25, 30 );
	// Hintergrundfarbe
	ImageColorAllocate( $im, 255, 255, 255 );
	// Textfarbe
	$textColor = ImageColorAllocate( $im, 0, 0, 0 );

	for ($i = 0; $i < $hashLength; $i++) {
		$textSize = rand(10, 15);
		$angle = rand(0, 30);
		$posLeft = 22 * $i + 10;
		$posTop = rand(20, 25);
		ImageTTFText( $im, $textSize, $angle, $posLeft, $posTop, $textColor, 'modules/captcha/trebuc.ttf', $hash[$i] );
	}
	ImageGif( $im );
	ImageDestroy( $im );
	exit;
}
?>