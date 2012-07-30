<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
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

	$settings = ACP3_Config::getModuleSettings('gallery');

	for ($i = 0; $i < $c_pictures; ++$i) {
		$picInfos = getimagesize(ACP3_ROOT . 'uploads/gallery/' . $pictures[$i]['file']);
		if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
			$newHeight = $settings['thumbheight'];
			$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
		}

		$pictures[$i]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
		$pictures[$i]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
	}

	return ACP3_Cache::create('gallery_pics_id_' . $id, $pictures);
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
	if (ACP3_Cache::check('gallery_pics_id_' . $id) === false)
		setGalleryCache($id);

	return ACP3_Cache::output('gallery_pics_id_' . $id);
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
	$alias = ACP3_SEO::getUriAlias('gallery/pics/id_' . $picture[0]['gallery_id'], true);
	if (!empty($alias))
		$alias.= '/' . makeStringUrlSafe($lang->t('gallery', 'picture')) . '-' . $picture_id;
	$seo_keywords = ACP3_SEO::getKeywords('gallery/pics/id_' . $picture[0]['gallery_id']);
	$seo_description = ACP3_SEO::getDescription('gallery/pics/id_' . $picture[0]['gallery_id']);

	return ACP3_SEO::insertUriAlias('gallery/details/id_' . $picture_id, $alias, $seo_keywords, $seo_description);
}
/**
 * Setzt alle Bild-Aliase einer Fotogalerie neu
 *
 * @param integer $gallery_id
 * @return boolean
 */
function generatePictureAliases($gallery_id)
{
	global $db, $lang;

	$pictures = $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $gallery_id . '\'');
	$c_pictures = count($pictures);
	$bool = false;

	$alias = ACP3_SEO::getUriAlias('gallery/pics/id_' . $gallery_id, true);
	if (!empty($alias))
		$alias.= '/' . makeStringUrlSafe($lang->t('gallery', 'picture'));
	$seo_keywords = ACP3_SEO::getKeywords('gallery/pics/id_' . $picture[0]['gallery_id']);
	$seo_description = ACP3_SEO::getDescription('gallery/pics/id_' . $picture[0]['gallery_id']);

	for ($i = 0; $i < $c_pictures; ++$i) {
		$bool = ACP3_SEO::insertUriAlias(!empty($alias) ? $alias . '-' . $pictures[$i]['id'] : '', 'gallery/details/id_' . $pictures[$i]['id'], $seo_keywords, $seo_description);
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
		$bool = ACP3_SEO::deleteUriAlias('gallery/details/id_' . $pictures[$i]['id']);
		if ($bool === false)
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