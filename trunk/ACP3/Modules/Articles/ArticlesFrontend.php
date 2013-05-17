<?php

namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Description of ArticlesFrontend
 *
 * @author Tino
 */
class ArticlesFrontend extends Core\ModuleController {

	public function actionList()
	{
		$period = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$time = Core\Registry::get('Date')->getCurrentDateTime();

		$articles = Core\Registry::get('Db')->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles WHERE ' . $period . ' ORDER BY title ASC LIMIT ' . POS . ',' . Core\Registry::get('Auth')->entries, array('time' => $time));
		$c_articles = count($articles);

		if ($c_articles > 0) {
			Core\Registry::get('View')->assign('pagination', Core\Functions::pagination(Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE ' . $period, array('time' => $time))));

			for ($i = 0; $i < $c_articles; ++$i) {
				$articles[$i]['date_formatted'] = Core\Registry::get('Date')->format($articles[$i]['start']);
				$articles[$i]['date_iso'] = Core\Registry::get('Date')->format($articles[$i]['start'], 'c');
			}

			Core\Registry::get('View')->assign('articles', $articles);
		}
	}

	public function actionDetails()
	{
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';

		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = :id' . $period, array('id' => Core\Registry::get('URI')->id, 'time' => Core\Registry::get('Date')->getCurrentDateTime())) == 1) {
			$page = ArticlesFunctions::getArticlesCache(Core\Registry::get('URI')->id);

			Core\Registry::get('Breadcrumb')->replaceAnchestor($page['title'], 0, true);

			Core\Registry::get('View')->assign('page', splitTextIntoPages(Core\Functions::rewriteInternalUri($page['text']), Core\Registry::get('URI')->getCleanQuery()));
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

}