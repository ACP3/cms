<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
/**
 * Erstellt den Galerie-Cache anhand der angegebenen ID
 *
 * @param integer $id
 *  Die ID der zu cachenden Galerie
 * @return boolean
 */
function setGalleryCache($id)
{
	global $db;

	$pictures = $db->select('id, file, description', 'gallery_pictures', 'gallery_id = \'' . $id . '\'', 'pic ASC, id ASC');
	$c_pictures = count($pictures);

	$settings = config::output('gallery');

	for ($i = 0; $i < $c_pictures; ++$i) {
		$picInfos = getimagesize(ACP3_ROOT . 'uploads/gallery/' . $pictures[$i]['file']);
		if ($picInfos[0] > $settings['thumbwidth'] && $picInfos[1] > $settings['thumbheight']) {
			if ($picInfos[0] > $picInfos[1]) {
				$newWidth = $settings['thumbwidth'];
				$newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
			} else {
				$newHeight = $settings['thumbheight'];
				$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
			}
		}

       	$pictures[$i]['width'] = isset($newWidth) ? $newWidth : $width;
       	$pictures[$i]['height'] = isset($newHeight) ? $newHeight : $height;
	}

	return cache::create('gallery_pics_id_' . $id, $pictures);
}
/**
 * Bindet die gecachete Galerie anhand ihrer ID ein
 *
 * @param integer $id
 *  Die ID der Galerie
 * @return array
 */
function getGalleryCache($id)
{
	if (!cache::check('gallery_pics_id_' . $id))
		setGalleryCache($id);

	return cache::output('gallery_pics_id_' . $id);
}