<?php
namespace ACP3\Modules\ACP3\Search\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class DisplaySearchResults
 * @package ACP3\Modules\ACP3\Search\Event
 */
class DisplaySearchResults extends Event
{
    /**
     * @var array
     */
    private $modules;
    /**
     * @var string
     */
    private $searchTerm;
    /**
     * @var string
     */
    private $areas;
    /**
     * @var string
     */
    private $sortDirection;
    /**
     * @var array
     */
    private $searchResults = [];

    /**
     * @param array  $modules
     * @param string $searchTerm
     * @param string $areas
     * @param string $sortDirection
     */
    public function __construct(array $modules, $searchTerm, $areas, $sortDirection)
    {
        $this->modules = $modules;
        $this->searchTerm = $searchTerm;
        $this->areas = $areas;
        $this->sortDirection = $sortDirection;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @return string
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @return array
     */
    public function getSearchResults()
    {
        return $this->searchResults;
    }

    /**
     * @param string $moduleName
     * @param array  $searchResults
     */
    public function addSearchResultsByModule($moduleName, array $searchResults)
    {
        $this->searchResults[$moduleName] = $searchResults;
    }
}
