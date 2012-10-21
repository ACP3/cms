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
	$pictures = ACP3_CMS::$db2->fetchAll('SELECT id, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC, id ASC', array($id));
	$c_pictures = count($pictures);

	$settings = ACP3_Config::getSettings('gallery');

	for ($i = 0; $i < $c_pictures; ++$i) {
		$picInfos = getimagesize(UPLOADS_DIR . 'gallery/' . $pictures[$i]['file']);
		if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
			$newHeight = $settings['thumbheight'];
			$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
		}

		$pictures[$i]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
		$pictures[$i]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
	}

	return ACP3_Cache::create('pics_id_' . $id, $pictures, 'gallery');
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
	if (ACP3_Cache::check('pics_id_' . $id, 'gallery') === false)
		setGalleryCache($id);

	return ACP3_Cache::output('pics_id_' . $id, 'gallery');
}
/**
 * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
 *
 * @param integer $picture_id
 * @return boolean
 */
function generatePictureAlias($picture_id)
{
	$gallery_id = ACP3_CMS::$db2->fetchColumn('SELECT gallery_id FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($picture_id));
	$alias = ACP3_SEO::getUriAlias('gallery/pics/id_' . $gallery_id, true);
	if (!empty($alias))
		$alias.= '/' . makeStringUrlSafe(ACP3_CMS::$lang->t('gallery', 'picture')) . '-' . $picture_id;
	$seo_keywords = ACP3_SEO::getKeywords('gallery/pics/id_' . $gallery_id);
	$seo_description = ACP3_SEO::getDescription('gallery/pics/id_' . $gallery_id);

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
	$pictures = ACP3_CMS::$db2->fetchAll('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($gallery_id));
	$c_pictures = count($pictures);
	$bool = false;

	$alias = ACP3_SEO::getUriAlias('gallery/pics/id_' . $gallery_id, true);
	if (!empty($alias))
		$alias.= '/' . makeStringUrlSafe(ACP3_CMS::$lang->t('gallery', 'picture'));
	$seo_keywords = ACP3_SEO::getKeywords('gallery/pics/id_' . $gallery_id);
	$seo_description = ACP3_SEO::getDescription('gallery/pics/id_' . $gallery_id);

	for ($i = 0; $i < $c_pictures; ++$i) {
		$bool = ACP3_SEO::insertUriAlias('gallery/details/id_' . $pictures[$i]['id'], !empty($alias) ? $alias . '-' . $pictures[$i]['id'] : '', $seo_keywords, $seo_description);
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
	$pictures = ACP3_CMS::$db2->fetchAll('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($gallery_id));
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