<?php

namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Description of ArticlesFrontend
 *
 * @author Tino
 */
class ArticlesFrontend extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionList()
	{
		$period = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
		$time = $this->injector['Date']->getCurrentDateTime();

		$articles = $this->injector['Db']->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles WHERE ' . $period . ' ORDER BY title ASC LIMIT ' . POS . ',' . $this->injector['Auth']->entries, array('time' => $time));
		$c_articles = count($articles);

		if ($c_articles > 0) {
			$this->injector['View']->assign('pagination', Core\Functions::pagination($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE ' . $period, array('time' => $time))));

			for ($i = 0; $i < $c_articles; ++$i) {
				$articles[$i]['date_formatted'] = $this->injector['Date']->format($articles[$i]['start']);
				$articles[$i]['date_iso'] = $this->injector['Date']->format($articles[$i]['start'], 'c');
			}

			$this->injector['View']->assign('articles', $articles);
		}
	}

	public function actionDetails()
	{
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';

		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = :id' . $period, array('id' => $this->injector['URI']->id, 'time' => $this->injector['Date']->getCurrentDateTime())) == 1) {
			$page = ArticlesFunctions::getArticlesCache($this->injector['URI']->id);

			$this->injector['Breadcrumb']->replaceAnchestor($page['title'], 0, true);

			$this->injector['View']->assign('page', splitTextIntoPages(Core\Functions::rewriteInternalUri($page['text']), $this->injector['URI']->getCleanQuery()));
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

}