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

	$settings = config::getModuleSettings('gallery');

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
/**
 * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
 *
 * @param integer $picture_id
 * @return boolean
 */
function generatePictureAlias($picture_id)
{
	global $db, $lang;

	$picture = $db->select('gallery_id', 'gallery_pictures', 'id = \'' . $picture_id . '\'');
	$alias = seo::getUriAlias('gallery/pics/id_' . $picture[0]['gallery_id']);
	$alias.= '-' . makeStringUrlSafe($lang->t('gallery', 'picture')) . '-' . $picture_id;
	$seo_keywords = seo::getKeywordsOrDescription('gallery/pics/id_' . $picture[0]['gallery_id']);
	$seo_description = seo::getKeywordsOrDescription('gallery/pics/id_' . $picture[0]['gallery_id'], 'description');
	return seo::insertUriAlias($alias, 'gallery/details/id_' . $picture_id, $seo_keywords, $seo_description);
}
/**
 * Setzt alle Bild-Aliase einer Fotogalerie neu
 *
 * @param integer $gallery_id
 * @return boolean
 */
function generatePictureAliases($gallery_id)
{
	global $db;

	$pictures = $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $gallery_id . '\'');
	$c_pictures = count($pictures);
	$bool = false;

	for ($i = 0; $i < $c_pictures; ++$i) {
		$bool = generatePictureAlias($pictures[$i]['id']);
	}

	return $bool;
}
/**
 * Sorgt dafür, dass wenn eine Fotogalerie gelöscht wird,
 * auch alle Bild-Aliase gelöscht werden
 *
 * @param integer $gallery_id
 * @return boolean
 */
function deletePictureAliases($gallery_id)
{
	global $db;

	$pictures = $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $gallery_id . '\'');
	$c_pictures = count($pictures);
	$bool = false;

	for ($i = 0; $i < $c_pictures; ++$i) {
		$bool = seo::deleteUriAlias('gallery/details/id_' . $pictures[$i]['id']);
		if (!$bool)
			break;
	}

	return $bool ? true : false;
}
/**
 * Löscht ein Bild aus dem Dateisystem
 *
 * @param string $file 
 */
function removePicture($file)
{
	removeUploadedFile('cache/images', 'gallery_thumb_' . $file);
	removeUploadedFile('cache/images', 'gallery_' . $file);
	removeUploadedFile('gallery', $file);
}