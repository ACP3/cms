<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class SEO
 * @package ACP3\Core
 */
class SEO
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFields;

    /**
     * SEO constructor.
     *
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements  $metaStatements
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields  $metaFormFields
     */
    public function __construct(
        MetaStatements $metaStatements,
        UriAliasManager $uriAliasManager,
        MetaFormFields $metaFormFields
    ) {
        $this->metaStatements = $metaStatements;
        $this->uriAliasManager = $uriAliasManager;
        $this->metaFormFields = $metaFormFields;
    }

    /**
     * Returns the SEO description of the given page
     *
     * @param string $path
     *
     * @return string
     * @deprecated
     */
    public function getDescription($path)
    {
        $this->metaStatements->getDescription($path);
    }

    /**
     * Returns the SEO keywords of the given page
     *
     * @param string $path
     *
     * @return string
     * @deprecated
     */
    public function getKeywords($path)
    {
        $this->metaStatements->getKeywords($path);
    }

    /**
     * Returns the SEO form fields
     *
     * @param string $path
     *
     * @return array
     * @deprecated
     */
    public function formFields($path = '')
    {
        return $this->metaFormFields->formFields($path);
    }

    /**
     * Deletes the given URL alias
     *
     * @param string $path
     *
     * @return boolean
     * @deprecated
     */
    public function deleteUriAlias($path)
    {
        return $this->uriAliasManager->deleteUriAlias($path);
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
