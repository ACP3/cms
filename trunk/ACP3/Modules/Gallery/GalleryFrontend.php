<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core;

/**
 * Description of GalleryFrontend
 *
 * @author Tino
 */
class GalleryFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionDetails() {
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, array('id' => $this->injector['URI']->id, 'time' => $this->injector['Date']->getCurrentDateTime())) > 0) {
			$picture = $this->injector['Db']->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.id, p.pic, p.file, p.description, p.comments FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array($this->injector['URI']->id));

			$settings = Core\Config::getSettings('gallery');

			// Brotkrümelspur
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('gallery', 'gallery'), $this->injector['URI']->route('gallery'))
					->append($picture['title'], $this->injector['URI']->route('gallery/pics/id_' . $picture['gallery_id']))
					->append($this->injector['Lang']->t('gallery', 'details'))
					->setTitlePrefix($picture['title'])
					->setTitlePostfix(sprintf($this->injector['Lang']->t('gallery', 'picture_x'), $picture['pic']));

			// Bildabmessungen berechnen
			$picture['width'] = $settings['width'];
			$picture['height'] = $settings['height'];
			$picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $picture['file']);
			if ($picInfos !== false) {
				if ($picInfos[0] > $settings['width'] || $picInfos[1] > $settings['height']) {
					if ($picInfos[0] > $picInfos[1]) {
						$newWidth = $settings['width'];
						$newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
					} else {
						$newHeight = $settings['height'];
						$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
					}
				}

				$picture['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
				$picture['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
			}

			$this->injector['View']->assign('picture', $picture);

			// Vorheriges Bild
			$picture_back = $this->injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
			if (!empty($picture_back)) {
				Core\SEO::setPreviousPage($this->injector['URI']->route('gallery/details/id_' . $picture_back));
				$this->injector['View']->assign('picture_back', $picture_back);
			}

			// Nächstes Bild
			$picture_next = $this->injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic > ? AND gallery_id = ? ORDER BY pic ASC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
			if (!empty($picture_next)) {
				Core\SEO::setNextPage($this->injector['URI']->route('gallery/details/id_' . $picture_next));
				$this->injector['View']->assign('picture_next', $picture_next);
			}

			if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
				require_once MODULES_DIR . 'comments/functions.php';

				$this->injector['View']->assign('comments', commentsList('gallery', $this->injector['URI']->id));
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionImage() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true) {
			@set_time_limit(20);
			$picture = $this->injector['Db']->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array($this->injector['URI']->id));
			$action = $this->injector['URI']->action === 'thumb' ? 'thumb' : '';

			$settings = Core\Config::getSettings('gallery');
			$options = array(
				'enable_cache' => CONFIG_CACHE_IMAGES == 1 ? true : false,
				'cache_prefix' => 'gallery_' . $action,
				'max_width' => $settings[$action . 'width'],
				'max_height' => $settings[$action . 'height'],
				'file' => UPLOADS_DIR . 'gallery/' . $picture,
				'prefer_height' => $action === 'thumb' ? true : false
			);

			$image = new Core\Image($options);
			$image->output();

			$this->injector['View']->setNoOutput(true);
		}
	}

	public function actionList() {
		$time = $this->injector['Date']->getCurrentDateTime();
		$where = '(g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)';
		$galleries = $this->injector['Db']->fetchAll('SELECT g.id, g.start, g.title, COUNT(p.gallery_id) AS pics FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) WHERE ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC LIMIT ' . POS . ',' . $this->injector['Auth']->entries, array('time' => $time));
		$c_galleries = count($galleries);

		if ($c_galleries > 0) {
			$this->injector['View']->assign('pagination', Core\Functions::pagination($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery AS g WHERE ' . $where, array('time' => $time))));

			$settings = Core\Config::getSettings('gallery');

			for ($i = 0; $i < $c_galleries; ++$i) {
				$galleries[$i]['date_formatted'] = $this->injector['Date']->format($galleries[$i]['start'], $settings['dateformat']);
				$galleries[$i]['date_iso'] = $this->injector['Date']->format($galleries[$i]['start'], 'c');
				$galleries[$i]['pics_lang'] = $galleries[$i]['pics'] . ' ' . $this->injector['Lang']->t('gallery', $galleries[$i]['pics'] == 1 ? 'picture' : 'pictures');
			}
			$this->injector['View']->assign('galleries', $galleries);
		}
	}

	public function actionPics() {
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = :id' . $period, array('id' => $this->injector['URI']->id, 'time' => $this->injector['Date']->getCurrentDateTime())) == 1) {
			// Cache der Galerie holen
			$pictures = GalleryFunctions::getGalleryCache($this->injector['URI']->id);
			$c_pictures = count($pictures);

			if ($c_pictures > 0) {
				$gallery_title = $this->injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array($this->injector['URI']->id));

				// Brotkrümelspur
				$this->injector['Breadcrumb']
						->append($this->injector['Lang']->t('gallery', 'gallery'), $this->injector['URI']->route('gallery'))
						->append($gallery_title);

				$settings = Core\Config::getSettings('gallery');

				for ($i = 0; $i < $c_pictures; ++$i) {
					$pictures[$i]['uri'] = $this->injector['URI']->route($settings['overlay'] == 1 ? 'gallery/image/id_' . $pictures[$i]['id'] . '/action_normal' : 'gallery/details/id_' . $pictures[$i]['id']);
					$pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
				}

				$this->injector['View']->assign('pictures', $pictures);
				$this->injector['View']->assign('overlay', (int) $settings['overlay']);
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$settings = Core\Config::getSettings('gallery');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$galleries = $this->injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => $this->injector['Date']->getCurrentDateTime()));
		$c_galleries = count($galleries);

		if ($c_galleries > 0) {
			for ($i = 0; $i < $c_galleries; ++$i) {
				$galleries[$i]['start'] = $this->injector['Date']->format($galleries[$i]['start']);
				$galleries[$i]['title_short'] = Core\Functions::shortenEntry($galleries[$i]['title'], 30, 5, '...');
			}
			$this->injector['View']->assign('sidebar_galleries', $galleries);
		}

		$this->injector['View']->displayTemplate('gallery/sidebar.tpl');
	}

}