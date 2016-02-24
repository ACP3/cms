<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Search;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Search\Controller\Frontend\Index
 */
class Index extends Core\Controller\FrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Search\Helpers
     */
    protected $searchHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Search\Validation\FormValidation
     */
    protected $searchValidator;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext       $context
     * @param \ACP3\Modules\ACP3\Search\Helpers                   $searchHelpers
     * @param \ACP3\Modules\ACP3\Search\Validation\FormValidation $searchValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Search\Helpers $searchHelpers,
        Search\Validation\FormValidation $searchValidator)
    {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
    }

    /**
     * @param string $q
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($q = '')
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        } elseif (!empty($q)) {
            return $this->executePost(['search_term' => (string)$q]);
        }

        // Zu durchsuchende Bereiche
        $langSearchAreas = [
            $this->translator->t('search', 'title_and_content'),
            $this->translator->t('search', 'title_only'),
            $this->translator->t('search', 'content_only')
        ];

        // Treffer sortieren
        $langSortHits = [$this->translator->t('search', 'asc'), $this->translator->t('search', 'desc')];

        return [
            'form' => array_merge(['search_term' => ''], $this->request->getPost()->all()),
            'search_mods' => $this->searchHelpers->getModules(),
            'search_areas' => $this->get('core.helpers.forms')->checkboxGenerator(
                'area',
                ['title_content', 'title', 'content'],
                $langSearchAreas,
                'title_content'
            ),
            'sort_hits' => $this->get('core.helpers.forms')->checkboxGenerator('sort', ['asc', 'desc'], $langSortHits, 'asc')
        ];
    }

    /**
     * @param array  $modules
     * @param string $searchTerm
     * @param string $area
     * @param string $sort
     */
    protected function displaySearchResults(array $modules, $searchTerm, $area, $sort)
    {
        $this->breadcrumb
            ->append($this->translator->t('search', 'search'), 'search')
            ->append($this->translator->t('search', 'search_results'));

        $searchResultsEvent = new Search\Event\DisplaySearchResults($modules, $searchTerm, $area, $sort);
        $this->eventDispatcher->dispatch('search.events.displaySearchResults', $searchResultsEvent);

        $searchResults = $searchResultsEvent->getSearchResults();
        if (!empty($searchResults)) {
            ksort($searchResults);
            $this->view->assign('results_mods', $searchResults);
        } else {
            $this->view->assign('no_search_results',
                $this->translator->t('search', 'no_search_results', ['%search_term%' => $searchTerm]));
        }

        $this->setTemplate('Search/Frontend/index.results.tpl');
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
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

                $this->displaySearchResults(
                    $formData['mods'],
                    Core\Functions::strEncode($formData['search_term']),
                    $formData['area'],
                    strtoupper($formData['sort'])
                );
            }
        );
    }
}
