<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

/**
 * Setzt das Cache für einen Download
 *
 * @param integer $id
 *	Die ID des zu cachenden Download
 * @return boolean
 */
function setFilesCache($id)
{
	global $db;
	return cache::create('files_details_id_' . $id, $db->select('f.id, f.start, f.category_id, f.file, f.size, f.link_title, f.text, f.comments, c.name AS category_name', 'files AS f, {pre}categories AS c', 'f.id = \'' . $id . '\' AND f.category_id = c.id'));
}
/**
 * Gibt den Cache eines Downloads aus
 *
 * @param integer $id
 *	ID des Downloads
 * @return array
 */
function getFilesCache($id)
{
	if (!cache::check('files_details_id_' . $id))
		setFilesCache($id);

	return cache::output('files_details_id_' . $id);
}

