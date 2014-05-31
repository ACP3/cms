<?php

namespace ACP3\Modules\Search\Controller;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Description of SearchFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Search\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Search\Model($this->db, $this->lang);
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validate($_POST);

                $this->session->unsetFormToken();

                $this->displaySearchResults($_POST['mods'], Core\Functions::strEncode($_POST['search_term']), $_POST['area'], strtoupper($_POST['sort']));
                return;
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'search');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        Core\Functions::getRedirectMessage();

        $this->view->assign('form', array_merge(array('search_term' => ''), $_POST));

        $this->view->assign('search_mods', Search\Helpers::getModules());

        // Zu durchsuchende Bereiche
        $langSearchAreas = array(
            $this->lang->t('search', 'title_and_content'),
            $this->lang->t('search', 'title_only'),
            $this->lang->t('search', 'content_only')
        );
        $this->view->assign('search_areas', Core\Functions::selectGenerator('area', array('title_content', 'title', 'content'), $langSearchAreas, 'title_content', 'checked'));

        // Treffer sortieren
        $langSortHits = array($this->lang->t('search', 'asc'), $this->lang->t('search', 'desc'));
        $this->view->assign('sort_hits', Core\Functions::selectGenerator('sort', array('asc', 'desc'), $langSortHits, 'asc', 'checked'));

        $this->session->generateFormToken();
    }

    protected function displaySearchResults($modules, $searchTerm, $area, $sort)
    {
        $this->breadcrumb
            ->append($this->lang->t('search', 'search'), 'search')
            ->append($this->lang->t('search', 'search_results'));

        $searchResults = array();
        foreach ($modules as $module) {
            $action = $module . 'Search';
            if (method_exists("\\ACP3\\Modules\\Search\\Extensions", $action) &&
                Core\Modules::hasPermission('frontend/' . $module) === true
            ) {
                $results = new Search\Extensions($area, $sort, $searchTerm);
                $searchResults = array_merge($searchResults, $results->$action());
            }
        }
        if (!empty($searchResults)) {
            ksort($searchResults);
            $this->view->assign('results_mods', $searchResults);
        } else {
            $this->view->assign('no_search_results', sprintf($this->lang->t('search', 'no_search_results'), $searchTerm));
        }

        $this->setContentTemplate('search/index.results.tpl');
    }

}