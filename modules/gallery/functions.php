<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
function setGalleryCache($id)
{
	global $db;
	return cache::create('gallery_pics_id_' . $id, $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $id . '\'', 'pic ASC, id ASC'));
}
function getGalleryCache($id)
{
	if (!cache::check('gallery_pics_id_' . $id)) {
		setGalleryCache($id);
	}
	return cache::output('gallery_pics_id_' . $id);
}
?>
