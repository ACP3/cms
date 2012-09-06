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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true) {
	if ((ACP3_CMS::$uri->action === 'up' || ACP3_CMS::$uri->action === 'down') &&
		ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
		moveOneStep(ACP3_CMS::$uri->action, 'gallery_pictures', 'id', 'pic', ACP3_CMS::$uri->id, 'gallery_id');

		$gallery_id = ACP3_CMS::$db2->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3_CMS::$uri->id));

		require_once MODULES_DIR . 'gallery/functions.php';
		setGalleryCache($gallery_id);

		ACP3_CMS::$uri->redirect('acp/gallery/edit/id_' . $gallery_id);
	}
}
ACP3_CMS::$uri->redirect('errors/404');
