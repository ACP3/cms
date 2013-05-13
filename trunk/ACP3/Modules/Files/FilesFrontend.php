<?php

namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Description of FilesFrontend
 *
 * @author Tino
 */
class FilesFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionList() {
		if (Core\Modules::isActive('categories') === true) {
			$categories = \ACP3\Modules\Categories\CategoriesFunctions::getCategoriesCache('files');
			if (count($categories) > 0) {
				$this->injector['View']->assign('categories', $categories);
			}
		}
	}

	public function actionDetails() {
		$period = ' AND (start = end AND start <= :time OR :time BETWEEN start AND end)';

		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = :id' . $period, array('id' => $this->injector['URI']->id, 'time' => $this->injector['Date']->getCurrentDateTime())) == 1) {
			$file = FilesFunctions::getFilesCache($this->injector['URI']->id);

			if ($this->injector['URI']->action === 'download') {
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
					$this->injector['URI']->redirect(0, $file['file']);
				} else {
					$this->injector['URI']->redirect('errors/404');
				}
			} else {
				// Brotkrümelspur
				$this->injector['Breadcrumb']->append($this->injector['Lang']->t('files', 'files'), $this->injector['URI']->route('files'))
						->append($file['category_name'], $this->injector['URI']->route('files/files/cat_' . $file['category_id']))
						->append($file['title']);

				$settings = Core\Config::getSettings('files');

				$file['size'] = !empty($file['size']) ? $file['size'] : $this->injector['Lang']->t('files', 'unknown_filesize');
				$file['date_formatted'] = $this->injector['Date']->format($file['start'], $settings['dateformat']);
				$file['date_iso'] = $this->injector['Date']->format($file['start'], 'c');
				$this->injector['View']->assign('file', $file);

				if ($settings['comments'] == 1 && $file['comments'] == 1 && Core\Modules::check('comments', 'list') === true) {
					$comments = new \ACP3\Modules\Comments\CommentsFrontend($this->injector, 'files', $this->injector['URI']->id);
					$this->injector['View']->assign('comments', $comments->actionList());
				}
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionFiles() {
		if (Core\Validate::isNumber($this->injector['URI']->cat) &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->injector['URI']->cat)) == 1) {
			$category = $this->injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($this->injector['URI']->cat));

			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('files', 'files'), $this->injector['URI']->route('files'))
					->append($category);

			$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
			$files = $this->injector['Db']->fetchAll('SELECT id, start, file, size, title FROM ' . DB_PRE . 'files WHERE category_id = :cat_id' . $period . ' ORDER BY start DESC, end DESC, id DESC', array('cat_id' => $this->injector['URI']->cat, 'time' => $this->injector['Date']->getCurrentDateTime()));
			$c_files = count($files);

			if ($c_files > 0) {
				$settings = Core\Config::getSettings('files');

				for ($i = 0; $i < $c_files; ++$i) {
					$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->injector['Lang']->t('files', 'unknown_filesize');
					$files[$i]['date_formatted'] = $this->injector['Date']->format($files[$i]['start'], $settings['dateformat']);
					$files[$i]['date_iso'] = $this->injector['Date']->format($files[$i]['start'], 'c');
				}
				$this->injector['View']->assign('files', $files);
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$settings = Core\Config::getSettings('files');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$files = $this->injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'files WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => $this->injector['Date']->getCurrentDateTime()));
		$c_files = count($files);

		if ($c_files > 0) {
			for ($i = 0; $i < $c_files; ++$i) {
				$files[$i]['start'] = $this->injector['Date']->format($files[$i]['start']);
				$files[$i]['title_short'] = Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
			}
			$this->injector['View']->assign('sidebar_files', $files);
		}

		$this->injector['View']->displayTemplate('files/sidebar.tpl');
	}

}