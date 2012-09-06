<?php
/**
 * Files
 *
 * @author Tino Goratsch
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
	$data = ACP3_CMS::$db2->fetchAssoc('SELECT f.id, f.start, f.category_id, f.file, f.size, f.link_title, f.text, f.comments, c.name AS category_name FROM ' . DB_PRE . 'files AS f, ' . DB_PRE . 'categories AS c WHERE f.id = ? AND f.category_id = c.id', array($id));
	return ACP3_Cache::create('details_id_' . $id, $data, 'files');
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
	if (ACP3_Cache::check('details_id_' . $id, 'files') === false)
		setFilesCache($id);

	return ACP3_Cache::output('details_id_' . $id, 'files');
}