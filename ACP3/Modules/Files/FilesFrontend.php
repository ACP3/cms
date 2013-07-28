<?php

namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Description of FilesFrontend
 *
 * @author Tino Goratsch
 */
class FilesFrontend extends Core\ModuleController {

	public function actionList() {
		if (Core\Modules::isActive('categories') === true) {
			$categories = \ACP3\Modules\Categories\CategoriesHelpers::getCategoriesCache('files');
			if (count($categories) > 0) {
				Core\Registry::get('View')->assign('categories', $categories);
			}
		}
	}

	public function actionDetails() {
		$period = ' AND (start = end AND start <= :time OR :time BETWEEN start AND end)';

		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = :id' . $period, array('id' => Core\Registry::get('URI')->id, 'time' => Core\Registry::get('Date')->getCurrentDateTime())) == 1) {
			$file = FilesHelpers::getFilesCache(Core\Registry::get('URI')->id);

			if (Core\Registry::get('URI')->action === 'download') {
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
					Core\Registry::get('URI')->redirect(0, $file['file']);
				} else {
					Core\Registry::get('URI')->redirect('errors/404');
				}
			} else {
				// Brotkrümelspur
				Core\Registry::get('Breadcrumb')->append(Core\Registry::get('Lang')->t('files', 'files'), Core\Registry::get('URI')->route('files'))
						->append($file['category_name'], Core\Registry::get('URI')->route('files/files/cat_' . $file['category_id']))
						->append($file['title']);

				$settings = Core\Config::getSettings('files');

				$file['size'] = !empty($file['size']) ? $file['size'] : Core\Registry::get('Lang')->t('files', 'unknown_filesize');
				$file['date_formatted'] = Core\Registry::get('Date')->format($file['start'], $settings['dateformat']);
				$file['date_iso'] = Core\Registry::get('Date')->format($file['start'], 'c');
				Core\Registry::get('View')->assign('file', $file);

				if ($settings['comments'] == 1 && $file['comments'] == 1 && Core\Modules::hasPermission('comments', 'list') === true) {
					$comments = new \ACP3\Modules\Comments\CommentsFrontend($this->injector, 'files', Core\Registry::get('URI')->id);
					Core\Registry::get('View')->assign('comments', $comments->actionList());
				}
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionFiles() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->cat) &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array(Core\Registry::get('URI')->cat)) == 1) {
			$category = Core\Registry::get('Db')->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array(Core\Registry::get('URI')->cat));

			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('files', 'files'), Core\Registry::get('URI')->route('files'))
					->append($category);

			$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
			$files = Core\Registry::get('Db')->fetchAll('SELECT id, start, file, size, title FROM ' . DB_PRE . 'files WHERE category_id = :cat_id' . $period . ' ORDER BY start DESC, end DESC, id DESC', array('cat_id' => Core\Registry::get('URI')->cat, 'time' => Core\Registry::get('Date')->getCurrentDateTime()));
			$c_files = count($files);

			if ($c_files > 0) {
				$settings = Core\Config::getSettings('files');

				for ($i = 0; $i < $c_files; ++$i) {
					$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : Core\Registry::get('Lang')->t('files', 'unknown_filesize');
					$files[$i]['date_formatted'] = Core\Registry::get('Date')->format($files[$i]['start'], $settings['dateformat']);
					$files[$i]['date_iso'] = Core\Registry::get('Date')->format($files[$i]['start'], 'c');
				}
				Core\Registry::get('View')->assign('files', $files);
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$settings = Core\Config::getSettings('files');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$files = Core\Registry::get('Db')->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'files WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => Core\Registry::get('Date')->getCurrentDateTime()));
		$c_files = count($files);

		if ($c_files > 0) {
			for ($i = 0; $i < $c_files; ++$i) {
				$files[$i]['start'] = Core\Registry::get('Date')->format($files[$i]['start']);
				$files[$i]['title_short'] = Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
			}
			Core\Registry::get('View')->assign('sidebar_files', $files);
		}

		Core\Registry::get('View')->displayTemplate('files/sidebar.tpl');
	}

}