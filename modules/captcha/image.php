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
	
	$im = imagecreate( 75, 23 );
	$black = ImageColorAllocate( $im, 255, 255, 255 );
	$white = ImageColorAllocate( $im, 0, 0, 0 );
	ImageTTFText( $im, 15, 0, 10, 17, $white, 'modules/captcha/trebuc.ttf', $hash );
	ImageGif( $im );
	ImageDestroy( $im );
	exit;
}
?>