<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Search;

class IndexPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Core\ACL $acl,
        private Core\Helpers\Secure $secureHelper,
        private Search\Helpers $searchHelpers,
        private Search\Validation\FormValidation $searchValidator,
        private Search\Utility\SearchAvailabilityRegistrar $availableModulesRegistrar,
        private Search\ViewProviders\SearchResultsViewProvider $searchResultsViewProvider
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?string $searchTerm = ''): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->actionHelper->handlePostAction(
            function () use ($searchTerm) {
                $formData = $this->prepareFormData(array_merge(['search_term' => $searchTerm], $this->request->getPost()->all()));

                $this->searchValidator->validate($formData);

                $this->setTemplate('Search/Frontend/index.results.tpl');

                $searchTerm = $this->secureHelper->strEncode($formData['search_term']);

                return ($this->searchResultsViewProvider)(
                    $this->processSearchResults(
                        $formData['mods'],
                        $searchTerm,
                        $formData['area'],
                        strtoupper($formData['sort'])
                    ),
                    $searchTerm
                );
            }
        );
    }

    private function prepareFormData(array $formData): array
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

    private function processSearchResults(array $modules, string $searchTerm, string $area, string $sort): array
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
        ksort($searchResults);

        return $searchResults;
    }
}
