<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\View\Block\Frontend;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Search\Utility\SearchAvailabilityRegistrar;

class SearchResultsBlock extends AbstractBlock
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var SearchAvailabilityRegistrar
     */
    private $availabilityRegistrar;

    /**
     * SearchResultsBlock constructor.
     *
     * @param BlockContext                $context
     * @param ACLInterface                $acl
     * @param SearchAvailabilityRegistrar $availabilityRegistrar
     */
    public function __construct(
        BlockContext $context,
        ACLInterface $acl,
        SearchAvailabilityRegistrar $availabilityRegistrar
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->availabilityRegistrar = $availabilityRegistrar;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->setTemplate('Search/Frontend/index.results.tpl');

        $data = $this->getData();

        $this->breadcrumb
            ->append($this->translator->t('search', 'search'), 'search')
            ->append($this->translator->t('search', 'search_results'));

        return [
            'results_mods' => $this->processSearchResults(
                $data['modules'],
                $data['search_term'],
                $data['area'],
                $data['sort']
            ),
            'search_term' => $data['search_term'],
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
    protected function processSearchResults(array $modules, $searchTerm, $area, $sort)
    {
        $searchResults = [];
        foreach ($this->availabilityRegistrar->getAvailableModules() as $moduleName => $searchAvailability) {
            if (\in_array($moduleName, $modules) && $this->acl->hasPermission('frontend/' . $moduleName)) {
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
