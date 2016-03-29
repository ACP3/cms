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
     * Returns the meta tags of the current page
     *
     * @return string
     * @deprecated
     */
    public function getMetaTags()
    {
        return $this->metaStatements->getMetaTags();
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
     * @param string $path
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     * @deprecated
     */
    protected function getSeoInformation($path, $key, $defaultValue = '')
    {
        return $this->metaStatements->getSeoInformation($path, $key, $defaultValue);
    }

    /**
     * Returns the SEO robots settings for the given page
     *
     * @param string $path
     *
     * @return string
     * @deprecated
     */
    public function getRobotsSetting($path = '')
    {
        return $this->metaStatements->getRobotsSetting($path);
    }

    /**
     * Sets a SEO description postfix for te current page
     *
     * @param string $postfix
     *
     * @return $this
     * @deprecated
     */
    public function setDescriptionPostfix($postfix)
    {
        $this->metaStatements->setDescriptionPostfix($postfix);

        return $this;
    }

    /**
     * Sets the canonical URL for the current page
     *
     * @param string $path
     *
     * @return $this
     * @deprecated
     */
    public function setCanonicalUri($path)
    {
        $this->metaStatements->setCanonicalUri($path);

        return $this;
    }

    /**
     * Sets the next page (useful for pagination)
     *
     * @param string $path
     *
     * @return $this
     * @deprecated
     */
    public function setNextPage($path)
    {
        $this->metaStatements->setNextPage($path);

        return $this;
    }

    /**
     * Sets the previous page (useful for pagination)
     *
     * @param string $path
     *
     * @return $this
     * @deprecated
     */
    public function setPreviousPage($path)
    {
        $this->metaStatements->setPreviousPage($path);

        return $this;
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
