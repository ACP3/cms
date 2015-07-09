<?php

namespace ACP3\Modules\ACP3\Search\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Search;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Search\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Modules\ACP3\Search\Helpers
     */
    protected $searchHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Search\Validator
     */
    protected $searchValidator;
    /**
     * @var \ACP3\Modules\ACP3\Search\Extensions
     */
    protected $searchExtensions;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Search\Helpers             $searchHelpers
     * @param \ACP3\Modules\ACP3\Search\Validator           $searchValidator
     * @param \ACP3\Modules\ACP3\Search\Extensions          $searchExtensions
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Search\Helpers $searchHelpers,
        Search\Validator $searchValidator,
        Search\Extensions $searchExtensions)
    {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
        $this->searchExtensions = $searchExtensions;
    }

    /**
     * @param string $q
     */
    public function actionIndex($q = '')
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->getAll());
        } elseif (!empty($q)) {
            $this->_indexPost(['search_term' => (string) $q]);
        }

        $this->view->assign('form', array_merge(['search_term' => ''], $this->request->getPost()->getAll()));

        $this->view->assign('search_mods', $this->searchHelpers->getModules());

        // Zu durchsuchende Bereiche
        $langSearchAreas = [
            $this->lang->t('search', 'title_and_content'),
            $this->lang->t('search', 'title_only'),
            $this->lang->t('search', 'content_only')
        ];
        $this->view->assign('search_areas', $this->get('core.helpers.forms')->selectGenerator('area', ['title_content', 'title', 'content'], $langSearchAreas, 'title_content', 'checked'));

        // Treffer sortieren
        $langSortHits = [$this->lang->t('search', 'asc'), $this->lang->t('search', 'desc')];
        $this->view->assign('sort_hits', $this->get('core.helpers.forms')->selectGenerator('sort', ['asc', 'desc'], $langSortHits, 'asc', 'checked'));
    }

    /**
     * @param array  $modules
     * @param string $searchTerm
     * @param string $area
     * @param string $sort
     */
    protected function _displaySearchResults(array $modules, $searchTerm, $area, $sort)
    {
        $this->breadcrumb
            ->append($this->lang->t('search', 'search'), 'search')
            ->append($this->lang->t('search', 'search_results'));

        $searchResults = [];
        foreach ($modules as $module) {
            $action = $module . 'Search';
            if (method_exists($this->searchExtensions, $action) &&
                $this->acl->hasPermission('frontend/' . $module) === true
            ) {
                $this->searchExtensions
                    ->setArea($area)
                    ->setSort($sort)
                    ->setSearchTerm($searchTerm);
                $searchResults = array_merge($searchResults, $this->searchExtensions->$action());
            }
        }
        if (!empty($searchResults)) {
            ksort($searchResults);
            $this->view->assign('results_mods', $searchResults);
        } else {
            $this->view->assign('no_search_results', sprintf($this->lang->t('search', 'no_search_results'), $searchTerm));
        }

        $this->setTemplate('Search/Frontend/index.results.tpl');
    }

    /**
     * @param array $formData
     */
    protected function _indexPost(array $formData)
    {
        try {
            if (isset($formData['search_term']) === true) {
                if (isset($formData['mods']) === false) {
                    $modules = $this->searchHelpers->getModules();

                    $formData['mods'] = [];
                    foreach ($modules as $row) {
                        $formData['mods'][] = $row['dir'];
                    }
                }
                if (isset($formData['area']) === false) {
                    $formData['area'] = 'title_content';
                }
                if (isset($formData['sort']) === false) {
                    $formData['sort'] = 'asc';
                }
            }

            $this->searchValidator->validate($formData);

            $this->_displaySearchResults(
                $formData['mods'],
                Core\Functions::strEncode($formData['search_term']),
                $formData['area'],
                strtoupper($formData['sort'])
            );
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
