<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Search;

class IndexPost extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Search\Helpers
     */
    private $searchHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Search\Validation\FormValidation
     */
    private $searchValidator;
    /**
     * @var Search\Utility\SearchAvailabilityRegistrar
     */
    private $availableModulesRegistrar;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Modules\ACP3\Search\ViewProviders\SearchResultsViewProvider
     */
    private $searchResultsViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Core\ACL $acl,
        Core\Helpers\Secure $secureHelper,
        Search\Helpers $searchHelpers,
        Search\Validation\FormValidation $searchValidator,
        Search\Utility\SearchAvailabilityRegistrar $availableModulesRegistrar,
        Search\ViewProviders\SearchResultsViewProvider $searchResultsViewProvider
    ) {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
        $this->searchValidator = $searchValidator;
        $this->availableModulesRegistrar = $availableModulesRegistrar;
        $this->secureHelper = $secureHelper;
        $this->acl = $acl;
        $this->searchResultsViewProvider = $searchResultsViewProvider;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?string $searchTerm = '')
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
