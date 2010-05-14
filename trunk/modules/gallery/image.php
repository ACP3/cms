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

if (validate::isNumber($uri->id) && ($uri->action == 'thumb' || $uri->action == 'normal')) {
	@set_time_limit(20);
	$picture = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');

	$path = ACP3_ROOT . 'uploads/gallery/' . $picture[0]['file'];

	if (is_file($path)) {
		$picInfo = getimagesize($path);
		$width = $picInfo[0];
		$height = $picInfo[1];
		$type = $picInfo[2];
		$action = $uri->action;

		$settings = config::output('gallery');

		header('Cache-Control: public');
		header('Pragma: public');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

		if (extension_loaded('gd') &&
			($type == '1' || $type == '2' || $type == '3') &&
			($action == 'thumb' && $width > $settings['thumbwidth'] && $height > $settings['thumbheight']) ||
			($action == 'normal' && $width > $settings['width'] && $height > $settings['height'])) {
			if ($width > $height) {
				$newWidth = $action == 'thumb' ? $settings['thumbwidth'] : $settings['width'];
				$newHeight = intval($height * $newWidth / $width);
			} else {
				$newHeight = $action == 'thumb' ? $settings['thumbheight'] : $settings['height'];
				$newWidth = intval($width * $newHeight / $height);
			}

			$newPic = imagecreatetruecolor($newWidth, $newHeight);
			switch ($type) {
				case '1':
					header('Content-type: image/gif');
					$oldPic = imagecreatefromgif($path);
					imagecopyresampled($newPic, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagegif($newPic);
					break;
				case '2':
					header('Content-type: image/jpeg');
					$oldPic = imagecreatefromjpeg($path);
					imagecopyresampled($newPic, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagejpeg($newPic, NULL, 90);
					break;
				case '3':
					header('Content-type: image/png');
					imagealphablending($newPic, false);
					$oldPic = imagecreatefrompng($path);
					imagecopyresampled($newPic, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
					imagesavealpha($newPic, true);
					imagepng($newPic);
					break;
			}
			imagedestroy($newPic);
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
			readfile($path);
		}
	}
	exit;
}