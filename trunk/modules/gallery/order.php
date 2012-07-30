<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true) {
	require_once MODULES_DIR . 'gallery/functions.php';

	if (($uri->action === 'up' || $uri->action === 'down') && $db->countRows('*', 'gallery_pictures', 'id = \'' . $uri->id . '\'') == 1) {
		moveOneStep($uri->action, 'gallery_pictures', 'id', 'pic', $uri->id, 'gallery');

		$gallery = $db->select('g.id', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
		setGalleryCache($gallery[0]['id']);

		$uri->redirect('acp/gallery/edit_gallery/id_' . $gallery[0]['id']);
	}
}
$uri->redirect('errors/404');
