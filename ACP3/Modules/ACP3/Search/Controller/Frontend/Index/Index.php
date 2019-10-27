<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Search;

class Index extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Search\Utility\SearchAvailabilityRegistrar
     */
    protected $availableModulesRegistrar;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext       $context
     * @param \ACP3\Core\Helpers\Forms                            $formsHelper
     * @param \ACP3\Core\Helpers\Secure                           $secureHelper
     * @param \ACP3\Modules\ACP3\Search\Helpers                   $searchHelpers
     * @param \ACP3\Modules\ACP3\Search\Validation\FormValidation $searchValidator
     * @param Search\Utility\SearchAvailabilityRegistrar          $availableModulesRegistrar
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\Secure $secureHelper,
        Search\Helpers $searchHelpers,
        Search\Validation\FormValidation $searchValidator,
        Search\Utility\SearchAvailabilityRegistrar $availableModulesRegistrar
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
        $this->availableModulesRegistrar = $availableModulesRegistrar;
        $this->secureHelper = $secureHelper;
    }

    /**
     * @param string $q
     *
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $q = '')
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all());
        }

        if (!empty($q)) {
            return $this->executePost(['search_term' => (string) $q]);
        }

        $searchAreas = [
            'title_content' => $this->translator->t('search', 'title_and_content'),
            'title' => $this->translator->t('search', 'title_only'),
            'content' => $this->translator->t('search', 'content_only'),
        ];

        $sortDirections = [
            'asc' => $this->translator->t('search', 'asc'),
            'desc' => $this->translator->t('search', 'desc'),
        ];

        return [
            'form' => \array_merge(['search_term' => ''], $this->request->getPost()->all()),
            'search_mods' => $this->searchHelpers->getModules(),
            'search_areas' => $this->formsHelper->checkboxGenerator(
                'area',
                $searchAreas,
                'title_content'
            ),
            'sort_hits' => $this->formsHelper->checkboxGenerator('sort', $sortDirections, 'asc'),
        ];
    }

    /**
     * @param array $formData
     *
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $formData = $this->prepareFormData($formData);

                $this->searchValidator->validate($formData);

                return $this->renderSearchResults(
                    $formData['mods'],
                    $this->secureHelper->strEncode($formData['search_term']),
                    $formData['area'],
                    \strtoupper($formData['sort'])
                );
            }
        );
    }

    /**
     * @param array $formData
     *
     * @return array
     */
    protected function prepareFormData(array $formData): array
    {
        if (isset($formData['search_term']) === true) {
            if (isset($formData['mods']) === false) {
                $modules = $this->searchHelpers->getModules();

                $formData['mods'] = [];
                foreach ($modules as $row) {
                    $formData['mods'][] = $row['name'];
                }
            }
            if (isset($formData['area']) === false) {
                $formData['area'] = 'title_content';
            }
            if (isset($formData['sort']) === false) {
                $formData['sort'] = 'asc';
            }
        }

        return $formData;
    }

    /**
     * @param array  $modules
     * @param string $searchTerm
     * @param string $area
     * @param string $sort
     *
     * @return array
     */
    protected function renderSearchResults(array $modules, string $searchTerm, string $area, string $sort): array
    {
        $this->breadcrumb
            ->append($this->translator->t('search', 'search'), 'search')
            ->append(
                $this->translator->t('search', 'search_results'),
                $this->request->getQuery()
            );

        $this->setTemplate('Search/Frontend/index.results.tpl');

        return [
            'results_mods' => $this->processSearchResults($modules, $searchTerm, $area, $sort),
            'search_term' => $searchTerm,
        ];
    }

    /**
     * @param array  $modules
     * @param string $searchTerm
     * @param string $area
     * @param string $sort
     *
     * @return array
     */
    protected function processSearchResults(array $modules, string $searchTerm, string $area, string $sort): array
    {
        $searchResults = [];
        foreach ($this->availableModulesRegistrar->getAvailableModules() as $moduleName => $searchAvailability) {
            if (\in_array($moduleName, $modules, true) && $this->acl->hasPermission('frontend/' . $moduleName)) {
                $results = $searchAvailability->fetchSearchResults($searchTerm, $area, $sort);

                if (!empty($results)) {
                    $searchResults[$moduleName] = $results;
                }
            }
        }
        \ksort($searchResults);

        return $searchResults;
    }
}
