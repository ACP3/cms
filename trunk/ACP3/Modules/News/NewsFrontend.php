<?php

namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Description of NewsFrontend
 *
 * @author Tino
 */
class NewsFrontend extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionList()
	{
		if (isset($_POST['cat']) && Core\Validate::isNumber($_POST['cat']) === true) {
			$cat = (int) $_POST['cat'];
		} elseif (Core\Validate::isNumber($this->injector['URI']->cat) === true) {
			$cat = (int) $this->injector['URI']->cat;
		} else {
			$cat = 0;
		}

		if (Core\Modules::check('categories', 'functions') === true) {
			require_once MODULES_DIR . 'categories/functions.php';
			$this->injector['View']->assign('categories', categoriesList('news', $cat));
		}

		$settings = Core\Config::getSettings('news');
		// Kategorie in Brotkrümelspur anzeigen
		if ($cat !== 0 && $settings['category_in_breadcrumb'] == 1) {
			Core\SEO::setCanonicalUri($this->injector['URI']->route('news'));
			$this->injector['Breadcrumb']->append($this->injector['Lang']->t('news', 'news'), $this->injector['URI']->route('news'));
			$category = $this->injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($cat));
			if (!empty($category)) {
				$this->injector['Breadcrumb']->append($category);
			}
		}

		// Falls Kategorie angegeben, News nur aus eben jener selektieren
		$cat = !empty($cat) ? ' AND category_id = ' . $cat : '';
		$time = $this->injector['Date']->getCurrentDateTime();
		$where = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' . $cat;

		$news = $this->injector['Db']->fetchAll('SELECT id, start, title, text, readmore, comments, uri FROM ' . DB_PRE . 'news WHERE ' . $where . ' ORDER BY start DESC, end DESC, id DESC LIMIT ' . POS . ',' . $this->injector['Auth']->entries, array('time' => $time));
		$c_news = count($news);

		if ($c_news > 0) {
			// Überprüfen, ob das Kommentare Modul aktiv ist
			if (Core\Modules::check('comments', 'functions') === true) {
				require_once MODULES_DIR . 'comments/functions.php';
				$comment_check = true;
			}

			$this->injector['View']->assign('pagination', Core\Functions::pagination($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE ' . $where, array('time' => $time))));

			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['date_formatted'] = $this->injector['Date']->format($news[$i]['start'], $settings['dateformat']);
				$news[$i]['date_iso'] = $this->injector['Date']->format($news[$i]['start'], 'c');
				$news[$i]['text'] = Core\Functions::rewriteInternalUri($news[$i]['text']);
				$news[$i]['allow_comments'] = false;
				if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && isset($comment_check)) {
					$news[$i]['comments'] = commentsCount('news', $news[$i]['id']);
					$news[$i]['allow_comments'] = true;
				}
				if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
					$news[$i]['text'] = Core\Functions::shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . $this->injector['URI']->route('news/details/id_' . $news[$i]['id']) . '">[' . $this->injector['Lang']->t('news', 'readmore') . "]</a>\n");
				}
			}
			$this->injector['View']->assign('news', $news);
		}
	}

	public function actionDetails()
	{
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = :id' . $period, array('id' => $this->injector['URI']->id, 'time' => $this->injector['Date']->getCurrentDateTime())) == 1) {

			$settings = Core\Config::getSettings('news');
			$news = NewsFunctions::getNewsCache($this->injector['URI']->id);

			$this->injector['Breadcrumb']->append($this->injector['Lang']->t('news', 'news'), $this->injector['URI']->route('news'));
			if ($settings['category_in_breadcrumb'] == 1) {
				// Brotkrümelspur
				$category = $this->injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($news['category_id']));
				if (!empty($category)) {
					$this->injector['Breadcrumb']->append($category, $this->injector['URI']->route('news/list/cat_' . $news['category_id']));
				}
			}
			$this->injector['Breadcrumb']->append($news['title']);

			$news['date_formatted'] = $this->injector['Date']->format($news['start'], $settings['dateformat']);
			$news['date_iso'] = $this->injector['Date']->format($news['start'], 'c');
			$news['text'] = Core\Functions::rewriteInternalUri($news['text']);
			if (!empty($news['uri']) && (bool) preg_match('=^http(s)?://=', $news['uri']) === false) {
				$news['uri'] = 'http://' . $news['uri'];
			}
			$news['target'] = $news['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

			$this->injector['View']->assign('news', $news);

			if ($settings['comments'] == 1 && $news['comments'] == 1 && Core\Modules::check('comments', 'functions') === true) {
				require_once MODULES_DIR . 'comments/functions.php';

				$this->injector['View']->assign('comments', commentsList('news', $this->injector['URI']->id));
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSidebar()
	{
		$settings = Core\Config::getSettings('news');

		$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$news = $this->injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'news WHERE ' . $where . ' ORDER BY start DESC, end DESC, id DESC LIMIT ' . $settings['sidebar'], array('time' => $this->injector['Date']->getCurrentDateTime()));
		$c_news = count($news);

		if ($c_news > 0) {
			for ($i = 0; $i < $c_news; ++$i) {
				$news[$i]['start'] = $this->injector['Date']->format($news[$i]['start'], $settings['dateformat']);
				$news[$i]['title'] = $news[$i]['title'];
				$news[$i]['title_short'] = Core\Functions::shortenEntry($news[$i]['title'], 30, 5, '...');
			}
			$this->injector['View']->assign('sidebar_news', $news);
		}

		$this->injector['View']->displayTemplate('news/sidebar.tpl');
	}

}