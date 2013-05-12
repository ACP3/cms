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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true) {
	if ((ACP3\CMS::$injector['URI']->action === 'up' || ACP3\CMS::$injector['URI']->action === 'down') &&
		ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
		moveOneStep(ACP3\CMS::$injector['URI']->action, 'gallery_pictures', 'id', 'pic', ACP3\CMS::$injector['URI']->id, 'gallery_id');

		$gallery_id = ACP3\CMS::$injector['Db']->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3\CMS::$injector['URI']->id));

		require_once MODULES_DIR . 'gallery/functions.php';
		setGalleryCache($gallery_id);

		ACP3\CMS::$injector['URI']->redirect('acp/gallery/edit/id_' . $gallery_id);
	}
}
ACP3\CMS::$injector['URI']->redirect('errors/404');
