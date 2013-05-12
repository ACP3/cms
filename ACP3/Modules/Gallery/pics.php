<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = :id' . $period, array('id' => ACP3\CMS::$injector['URI']->id, 'time' => ACP3\CMS::$injector['Date']->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	// Cache der Galerie holen
	$pictures = getGalleryCache(ACP3\CMS::$injector['URI']->id);
	$c_pictures = count($pictures);

	if ($c_pictures > 0) {
		$gallery_title = ACP3\CMS::$injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		// Brotkrümelspur
		ACP3\CMS::$injector['Breadcrumb']
		->append(ACP3\CMS::$injector['Lang']->t('gallery', 'gallery'), ACP3\CMS::$injector['URI']->route('gallery'))
		->append($gallery_title);

		$settings = ACP3\Core\Config::getSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['uri'] = ACP3\CMS::$injector['URI']->route($settings['overlay'] == 1 ? 'gallery/image/id_' . $pictures[$i]['id'] . '/action_normal' : 'gallery/details/id_' . $pictures[$i]['id']);
			$pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
		}

		ACP3\CMS::$injector['View']->assign('pictures', $pictures);
		ACP3\CMS::$injector['View']->assign('overlay', (int) $settings['overlay']);
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
