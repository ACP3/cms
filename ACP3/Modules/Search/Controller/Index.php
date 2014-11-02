<?php

namespace ACP3\Modules\Search\Controller;

use ACP3\Core;
use ACP3\Modules\Search;

/**
 * Class Index
 * @package ACP3\Modules\Search\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{

    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Helpers\Secure $secureHelper)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $this->redirectMessages()->getMessage();

        $this->view->assign('form', array_merge(array('search_term' => ''), $_POST));

        $this->view->assign('search_mods', $this->get('search.helpers')->getModules());

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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param $modules
     * @param $searchTerm
     * @param $area
     * @param $sort
     */
    protected function _displaySearchResults($modules, $searchTerm, $area, $sort)
    {
        $this->breadcrumb
            ->append($this->lang->t('search', 'search'), 'search')
            ->append($this->lang->t('search', 'search_results'));

        $searchResults = array();
        foreach ($modules as $module) {
            $action = $module . 'Search';
            if (method_exists($this->get('search.extensions'), $action) &&
                $this->acl->hasPermission('frontend/' . $module) === true
            ) {
                $results = $this->get('search.extensions');
                $results
                    ->setArea($area)
                    ->setSort($sort)
                    ->setSearchTerm($searchTerm);
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

    private function _indexPost(array $formData)
    {
        try {
            $validator = $this->get('search.validator');
            $validator->validate($formData);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->_displaySearchResults($formData['mods'], Core\Functions::strEncode($formData['search_term']), $formData['area'], strtoupper($formData['sort']));
            return;
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'search');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}