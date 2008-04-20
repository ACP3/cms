<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!validate::isNumber($modules->id) || $modules->action != 'mini' && $modules->action != 'thumb') {
	redirect('errors/404');
} else {
	@set_time_limit(20);
	$pic = $db->select('file', 'galpics', 'id = \'' . $modules->id . '\'');

	$pic = 'uploads/gallery/' . $pic[0]['file'];

	if (file_exists($pic)) {
		$pic_info = getimagesize($pic);
		$width = $pic_info[0];
		$height = $pic_info[1];
		$type = $pic_info[2];
		$action = $modules->action;

		if (extension_loaded('gd') && ($type == '1' || $type == '2' || $type == '3') && ($action == 'mini' && $width > 160 && $height > 120) || ($action == 'thumb' && $width > 640 && $height > 480)) {
			$t_height = $action == 'mini' ? 120 : 480;
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
	exit;
}
?>