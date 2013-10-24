<?php

namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Description of FilesFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\ModuleController {

	public function __construct() {
		parent::__construct();
	}

	public function actionList() {
		if (Core\Modules::isActive('categories') === true) {
			$categories = \ACP3\Modules\Categories\Helpers::getCategoriesCache('files');
			if (count($categories) > 0) {
				$this->view->assign('categories', $categories);
			}
		}
	}

	public function actionDetails() {
		$period = ' AND (start = end AND start <= :time OR :time BETWEEN start AND end)';

		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = :id' . $period, array('id' => $this->uri->id, 'time' => $this->date->getCurrentDateTime())) == 1) {
			$file = Helpers::getFilesCache($this->uri->id);

			if ($this->uri->action === 'download') {
				$path = UPLOADS_DIR . 'files/';
				if (is_file($path . $file['file'])) {
					// Schönen Dateinamen generieren
					$ext = strrchr($file['file'], '.');
					$filename = Core\Functions::makeStringUrlSafe($file['title']) . $ext;

					header('Content-Type: application/force-download');
					header('Content-Transfer-Encoding: binary');
					header('Content-Length:' . filesize($path . $file['file']));
					header('Content-Disposition: attachment; filename="' . $filename . '"');
					readfile($path . $file['file']);
					exit;
					// Externe Datei
				} elseif (preg_match('/^([a-z]+):\/\//', $file['file'])) {
					$this->uri->redirect(0, $file['file']);
				} else {
					$this->uri->redirect('errors/404');
				}
			} else {
				// Brotkrümelspur
				$this->breadcrumb->append($this->lang->t('files', 'files'), $this->uri->route('files'))
						->append($file['category_name'], $this->uri->route('files/files/cat_' . $file['category_id']))
						->append($file['title']);

				$settings = Core\Config::getSettings('files');

				$file['size'] = !empty($file['size']) ? $file['size'] : $this->lang->t('files', 'unknown_filesize');
				$file['date_formatted'] = $this->date->format($file['start'], $settings['dateformat']);
				$file['date_iso'] = $this->date->format($file['start'], 'c');
				$this->view->assign('file', $file);

				if ($settings['comments'] == 1 && $file['comments'] == 1 && Core\Modules::hasPermission('comments', 'list') === true) {
					$comments = new \ACP3\Modules\Comments\Frontend($this->injector, 'files', $this->uri->id);
					$this->view->assign('comments', $comments->actionList());
				}
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionFiles() {
		if (Core\Validate::isNumber($this->uri->cat) &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->uri->cat)) == 1) {
			$category = $this->db->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->uri->cat));

			$this->breadcrumb
					->append($this->lang->t('files', 'files'), $this->uri->route('files'))
					->append($category);

			$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
			$files = $this->db->fetchAll('SELECT id, start, file, size, title FROM ' . DB_PRE . 'files WHERE category_id = :cat_id' . $period . ' ORDER BY start DESC, end DESC, id DESC', array('cat_id' => $this->uri->cat, 'time' => $this->date->getCurrentDateTime()));
			$c_files = count($files);

			if ($c_files > 0) {
				$settings = Core\Config::getSettings('files');

				for ($i = 0; $i < $c_files; ++$i) {
					$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->lang->t('files', 'unknown_filesize');
					$files[$i]['date_formatted'] = $this->date->format($files[$i]['start'], $settings['dateformat']);
					$files[$i]['date_iso'] = $this->date->format($files[$i]['start'], 'c');
				}
				$this->view->assign('files', $files);
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$settings = Core\Config::getSettings('files');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$files = $this->db->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'files WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => $this->date->getCurrentDateTime()));
		$c_files = count($files);

		if ($c_files > 0) {
			for ($i = 0; $i < $c_files; ++$i) {
				$files[$i]['start'] = $this->date->format($files[$i]['start']);
				$files[$i]['title_short'] = Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
			}
			$this->view->assign('sidebar_files', $files);
		}

		$this->view->displayTemplate('files/sidebar.tpl');
	}

}