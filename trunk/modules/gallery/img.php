<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (empty($_GET['id']) || !ereg('[0-9]', $_GET['id']) || empty($_GET['mode']) || $_GET['mode'] != 'mini' && $_GET['mode'] != 'thumb')
	exit;

@set_time_limit(20);
error_reporting(0);

define('IN_ACP3', true);

require '../../includes/globals.php';
require '../../includes/config.php';
if (!defined('INSTALLED')) {
	header('Location: ../../installation/');
	exit;
}
require '../../includes/classes/db.php';

$db = new db;

$pic = $db->select('file', 'galpics', 'id = \'' . $_GET['id'] . '\'');

$pic = '../../files/gallery/' . $pic[0]['file'];

if (file_exists($pic)) {
	$pic_info = getimagesize($pic);
	$width = $pic_info[0];
	$height = $pic_info[1];
	$type = $pic_info[2];
	$mode = $_GET['mode'];

	if (extension_loaded('gd') && ($type == '1' || $type == '2' || $type == '3') && ($mode == 'mini' && $width > 160 && $height > 120) || ($mode == 'thumb' && $width > 640 && $height > 480)) {
		$t_height = $mode == 'mini' ? 120 : 480;
		$t_width = intval($width * $t_height / $height);

		$pic_new = imagecreatetruecolor($t_width, $t_height);
		switch ($type) {
			case '1':
				header('Content-type: image/gif');
				$pic_old = imagecreatefromgif($pic);
				imagecopyresampled($pic_new, $pic_old, 0, 0, 0, 0, $t_width, $t_height, $width, $height);
				imagegif($pic_new);
				break;
			case '2':
				header('Content-type: image/jpeg');
				$pic_old = imagecreatefromjpeg($pic);
				imagecopyresampled($pic_new, $pic_old, 0, 0, 0, 0, $t_width, $t_height, $width, $height);
				imagejpeg($pic_new, NULL, 90);
				break;
			case '3':
				header('Content-type: image/png');
				imagealphablending($pic_new, false);
				$pic_old = imagecreatefrompng($pic);
				imagecopyresampled($pic_new, $pic_old, 0, 0, 0, 0, $t_width, $t_height, $width, $height);
				imagesavealpha($pic_new, true);
				imagepng($pic_new);
				break;
		}
		imagedestroy($pic_new);
	} else {
		switch ($type) {
			case '1':
				header('Content-type: image/gif');
				break;
			case '2':
				header('Content-type: image/jpeg');
				break;
			case '3':
				header('Content-type: image/png');
				break;
			default:
				exit;
		}
		readfile($pic);
	}
}
?>