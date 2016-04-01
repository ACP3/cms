<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class SEO
 * @package ACP3\Core
 */
class SEO
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * SEO constructor.
     *
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function __construct(UriAliasManager $uriAliasManager) {
        $this->uriAliasManager = $uriAliasManager;
    }

    /**
     * Inserts/Updates a given URL alias
     *
     * @param string $path
     * @param string $alias
     * @param string $keywords
     * @param string $description
     * @param int    $robots
     *
     * @return boolean
     * @deprecated
     */
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        return $this->uriAliasManager->insertUriAlias($path, $alias, $keywords, $description, $robots);
    }
}
