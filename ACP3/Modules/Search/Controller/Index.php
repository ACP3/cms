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
    /**
     * @var \ACP3\Modules\Search\Helpers
     */
    protected $searchHelpers;
    /**
     * @var \ACP3\Modules\Search\Validator
     */
    protected $searchValidator;
    /**
     * @var \ACP3\Modules\Search\Extensions
     */
    protected $searchExtensions;

    /**
     * @param \ACP3\Core\Context\Frontend     $context
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Modules\Search\Helpers    $searchHelpers
     * @param \ACP3\Modules\Search\Validator  $searchValidator
     * @param \ACP3\Modules\Search\Extensions $searchExtensions
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Helpers\Secure $secureHelper,
        Search\Helpers $searchHelpers,
        Search\Validator $searchValidator,
        Search\Extensions $searchExtensions)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
        $this->searchExtensions = $searchExtensions;
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $this->view->assign('form', array_merge(['search_term' => ''], $_POST));

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
    private function _indexPost(array $formData)
    {
        try {
            $this->searchValidator->validate($formData);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->_displaySearchResults($formData['mods'], Core\Functions::strEncode($formData['search_term']), $formData['area'], strtoupper($formData['sort']));
            return;
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
