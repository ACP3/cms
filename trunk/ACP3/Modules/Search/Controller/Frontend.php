<?php

namespace ACP3\Modules\Search\Controller;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Description of SearchFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);
    }

    public function actionList()
    {
        if (isset($_POST['submit']) === true) {
            if (strlen($_POST['search_term']) < 3)
                $errors['search-term'] = $this->lang->t('search', 'search_term_to_short');
            if (empty($_POST['mods']))
                $errors[] = $this->lang->t('search', 'no_module_selected');
            if (empty($_POST['area']))
                $errors[] = $this->lang->t('search', 'no_area_selected');
            if (empty($_POST['sort']) || $_POST['sort'] != 'asc' && $_POST['sort'] != 'desc')
                $errors[] = $this->lang->t('search', 'no_sorting_selected');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } else {
                $this->displaySearchResults($_POST['mods'], Core\Functions::strEncode($_POST['search_term']), $_POST['area'], strtoupper($_POST['sort']));
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('search_term' => ''));

            $this->view->assign('search_mods', Search\Helpers::getModules());

            // Zu durchsuchende Bereiche
            $lang_search_areas = array(
                $this->lang->t('search', 'title_and_content'),
                $this->lang->t('search', 'title_only'),
                $this->lang->t('search', 'content_only')
            );
            $this->view->assign('search_areas', Core\Functions::selectGenerator('area', array('title_content', 'title', 'content'), $lang_search_areas, 'title_content', 'checked'));

            // Treffer sortieren
            $lang_sort_hits = array($this->lang->t('search', 'asc'), $this->lang->t('search', 'desc'));
            $this->view->assign('sort_hits', Core\Functions::selectGenerator('sort', array('asc', 'desc'), $lang_sort_hits, 'asc', 'checked'));
        }
    }

    protected function displaySearchResults($modules, $searchTerm, $area, $sort)
    {
        $this->breadcrumb
            ->append($this->lang->t('search', 'search'), $this->uri->route('search'))
            ->append($this->lang->t('search', 'search_results'));

        $searchResults = array();
        foreach ($modules as $module) {
            $action = $module . 'Search';
            if (method_exists("\\ACP3\\Modules\\Search\\Extensions", $action) &&
                Core\Modules::hasPermission($module, 'list') === true
            ) {
                $results = new Extensions($area, $sort, $searchTerm);
                $searchResults = array_merge($searchResults, $results->$action());
            }
        }
        if (!empty($searchResults)) {
            ksort($searchResults);
            $this->view->assign('results_mods', $searchResults);
        } else {
            $this->view->assign('no_search_results', sprintf($this->lang->t('search', 'no_search_results'), $searchTerm));
        }

        $this->view->setContentTemplate('search/results.tpl');
    }

    public function actionSidebar()
    {
        $this->view->assign('search_mods', Search\Helpers::getModules());

        $this->view->displayTemplate('search/sidebar.tpl');
    }

}