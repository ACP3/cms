<?php

/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Gallery;

use ACP3\Core;

class GalleryFunctions {

	/**
	 * Erstellt den Galerie-Cache anhand der angegebenen ID
	 *
	 * @param integer $id
	 *  Die ID der zu cachenden Galerie
	 * @return boolean
	 */
	public static function setGalleryCache($id) {
		$pictures = Core\Registry::get('Db')->fetchAll('SELECT id, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC, id ASC', array($id));
		$c_pictures = count($pictures);

		$settings = Core\Config::getSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['width'] = $settings['thumbwidth'];
			$pictures[$i]['height'] = $settings['thumbheight'];
			$picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $pictures[$i]['file']);
			if ($picInfos !== false) {
				if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
					$newHeight = $settings['thumbheight'];
					$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
				}

				$pictures[$i]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
				$pictures[$i]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
			}
		}

		return Core\Cache::create('pics_id_' . $id, $pictures, 'gallery');
	}

	/**
	 * Bindet die gecachete Galerie anhand ihrer ID ein
	 *
	 * @param integer $id
	 *  Die ID der Galerie
	 * @return array
	 */
	public static function getGalleryCache($id) {
		if (Core\Cache::check('pics_id_' . $id, 'gallery') === false)
			self::setGalleryCache($id);

		return Core\Cache::output('pics_id_' . $id, 'gallery');
	}

	/**
	 * Setzt einen einzelnen Alias für ein Bild einer Fotogalerie
	 *
	 * @param integer $picture_id
	 * @return boolean
	 */
	public static function generatePictureAlias($picture_id) {
		$gallery_id = Core\Registry::get('Db')->fetchColumn('SELECT gallery_id FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($picture_id));
		$alias = Core\SEO::getUriAlias('gallery/pics/id_' . $gallery_id, true);
		if (!empty($alias))
			$alias.= '/img-' . $picture_id;
		$seo_keywords = Core\SEO::getKeywords('gallery/pics/id_' . $gallery_id);
		$seo_description = Core\SEO::getDescription('gallery/pics/id_' . $gallery_id);

		return Core\SEO::insertUriAlias('gallery/details/id_' . $picture_id, $alias, $seo_keywords, $seo_description);
	}

	/**
	 * Setzt alle Bild-Aliase einer Fotogalerie neu
	 *
	 * @param integer $gallery_id
	 * @return boolean
	 */
	public static function generatePictureAliases($gallery_id) {
		$pictures = Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($gallery_id));
		$c_pictures = count($pictures);
		$bool = false;

		$alias = Core\SEO::getUriAlias('gallery/pics/id_' . $gallery_id, true);
		if (!empty($alias))
			$alias.= '/img';
		$seo_keywords = Core\SEO::getKeywords('gallery/pics/id_' . $gallery_id);
		$seo_description = Core\SEO::getDescription('gallery/pics/id_' . $gallery_id);

		for ($i = 0; $i < $c_pictures; ++$i) {
			$bool = Core\SEO::insertUriAlias('gallery/details/id_' . $pictures[$i]['id'], !empty($alias) ? $alias . '-' . $pictures[$i]['id'] : '', $seo_keywords, $seo_description);
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
	public static function deletePictureAliases($gallery_id) {
		$pictures = Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array($gallery_id));
		$c_pictures = count($pictures);
		$bool = false;

		for ($i = 0; $i < $c_pictures; ++$i) {
			$bool = Core\SEO::deleteUriAlias('gallery/details/id_' . $pictures[$i]['id']);
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
	public static function removePicture($file) {
		Core\Functions::removeUploadedFile('cache/images', 'gallery_thumb_' . $file);
		Core\Functions::removeUploadedFile('cache/images', 'gallery_' . $file);
		Core\Functions::removeUploadedFile('gallery', $file);
	}

}