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
	return cache::create('gallery_pics_id_' . $id, $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $id . '\'', 'pic ASC, id ASC'));
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
?>
