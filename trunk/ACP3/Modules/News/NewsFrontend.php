<?php

namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Description of NewsFrontend
 *
 * @author Tino
 */
class NewsFrontend extends Core\ModuleController {

	public function actionDetails()
	{
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = :id' . $period, array('id' => Core\Registry::get('URI')->id, 'time' => Core\Registry::get('Date')->getCurrentDateTime())) == 1) {

			$settings = Core\Config::getSettings('news');
			$news = NewsFunctions::getNewsCache(Core\Registry::get('URI')->id);

			Core\Registry::get('Breadcrumb')->append(Core\Registry::get('Lang')->t('news', 'news'), Core\Registry::get('URI')->route('news'));
			if ($settings['category_in_breadcrumb'] == 1) {
				// Brotkrümelspur
				$category = Core\Registry::get('Db')->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($news['category_id']));
				if (!empty($category)) {
					Core\Registry::get('Breadcrumb')->append($category, Core\Registry::get('URI')->route('news/list/cat_' . $news['category_id']));
				}
			}
			Core\Registry::get('Breadcrumb')->append($news['title']);

			$news['date_formatted'] = Core\Registry::get('Date')->format($news['start'], $settings['dateformat']);
			$news['date_iso'] = Core\Registry::get('Date')->format($news['start'], 'c');
			$news['text'] = Core\Functions::rewriteInternalUri($news['text']);
			if (!empty($news['uri']) && (bool) preg_match('=^http(s)?://=', $news['uri']) === false) {
				$news['uri'] = 'http://' . $news['uri'];
			}
			$news['target'] = $news['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

			Core\Registry::get('View')->assign('news', $news);

			if ($settings['comments'] == 1 && $news['comments'] == 1 && Core\Modules::check('comments', 'list') === true) {
				$comments = new \ACP3\Modules\Comments\CommentsFrontend($this->injector, 'news', Core\Registry::get('URI')->id);
				Core\Registry::get('View')->assign('comments', $comments->actionList());
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList()
	{
		if (isset($_POST['cat']) && Core\Validate::isNumber($_POST['cat']) === true) {
			$cat = (int) $_POST['cat'];
		} elseif (Core\Validate::isNumber(Core\Registry::get('URI')->cat) === true) {
			$cat = (int) Core\Registry::get('URI')->cat;
		} else {
			$cat = 0;
		}

		if (Core\Modules::isActive('categories') === true) {
			Core\Registry::get('View')->assign('categories', \ACP3\Modules\Categories\CategoriesFunctions::categoriesList('news', $cat));
		}

		$settings = Core\Config::getSettings('news');
		// Kategorie in Brotkrümelspur anzeigen
		if ($cat !== 0 && $settings['category_in_breadcrumb'] == 1) {
			Core\SEO::setCanonicalUri(Core\Registry::get('URI')->route('news'));
			Core\Registry::get('Breadcrumb')->append(Core\Registry::get('Lang')->t('news', 'news'), Core\Registry::get('URI')->route('news'));
			$category = Core\Registry::get('Db')->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($cat));
			if (!empty($category)) {
				Core\Registry::get('Breadcrumb')->append($category);
			}
		}

		// Falls Kategorie angegeben, News nur aus eben jener selektieren
		$cat = !empty($cat) ? ' AND category_id = ' . $cat : '';
		$time = Core\Registry::get('Date')->getCurrentDateTime();
		$where = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' . $cat;

		$news = Core\Registry::get('Db')->fetchAll('SELECT id, start, title, text, readmore, comments, uri FROM ' . DB_PRE . 'news WHERE ' . $where . ' ORDER BY start DESC, end DESC, id DESC LIMIT ' . POS . ',' . Core\Registry::get('Auth')->entries, array('time' => $time));
		$c_news = count($news);

		if ($c_news > 0) {
			$comment_check = false;
			// Überprüfen, ob das Kommentare Modul aktiv ist
			if (Core\Modules::isActive('comments') === true) {
				$comment_check = true;
			}

			Core\Registry::get('View')->assign('pagination', Core\Functions::pagination(Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE ' . $where, array('time' => $time))));

			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['date_formatted'] = Core\Registry::get('Date')->format($news[$i]['start'], $settings['dateformat']);
				$news[$i]['date_iso'] = Core\Registry::get('Date')->format($news[$i]['start'], 'c');
				$news[$i]['text'] = Core\Functions::rewriteInternalUri($news[$i]['text']);
				if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && $comment_check === true) {
					$news[$i]['comments_count'] = \ACP3\Modules\Comments\CommentsFunctions::commentsCount('news', $news[$i]['id']);
				}
				if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
					$news[$i]['text'] = Core\Functions::shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . Core\Registry::get('URI')->route('news/details/id_' . $news[$i]['id']) . '">[' . Core\Registry::get('Lang')->t('news', 'readmore') . "]</a>\n");
				}
			}
			Core\Registry::get('View')->assign('news', $news);
		}
	}

	public function actionSidebar()
	{
		$settings = Core\Config::getSettings('news');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$news = Core\Registry::get('Db')->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'news WHERE ' . $where . ' ORDER BY start DESC, end DESC, id DESC LIMIT ' . $settings['sidebar'], array('time' => Core\Registry::get('Date')->getCurrentDateTime()));
		$c_news = count($news);

		if ($c_news > 0) {
			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['start'] = Core\Registry::get('Date')->format($news[$i]['start'], $settings['dateformat']);
				$news[$i]['title'] = $news[$i]['title'];
				$news[$i]['title_short'] = Core\Functions::shortenEntry($news[$i]['title'], 30, 5, '...');
			}
			Core\Registry::get('View')->assign('sidebar_news', $news);
		}

		Core\Registry::get('View')->displayTemplate('news/sidebar.tpl');
	}

}