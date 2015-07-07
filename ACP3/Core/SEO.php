<?php
namespace ACP3\Core;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Cache as SeoCache;
use ACP3\Modules\ACP3\Seo\Model as SeoModel;

/**
 * Class SEO
 * @package ACP3\Core
 */
class SEO
{
    /**
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model
     */
    protected $seoModel;

    /**
     * @var string
     */
    protected $nextPage = '';
    /**
     * @var string
     */
    protected $previousPage = '';
    /**
     * @var string
     */
    protected $canonicalUrl = '';
    /**
     * @var null|array
     */
    protected $aliasesCache = null;
    /**
     * @var string
     */
    protected $metaDescriptionPostfix = '';

    /**
     * @param \ACP3\Core\Lang              $lang
     * @param \ACP3\Core\RequestInterface  $request
     * @param \ACP3\Core\Router\Aliases    $aliases
     * @param \ACP3\Core\Helpers\Forms     $formsHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache $seoCache
     * @param \ACP3\Core\Config            $config
     * @param \ACP3\Modules\ACP3\Seo\Model $seoModel
     */
    public function __construct(
        Lang $lang,
        RequestInterface $request,
        Aliases $aliases,
        Forms $formsHelper,
        SeoCache $seoCache,
        Config $config,
        SeoModel $seoModel)
    {
        $this->lang = $lang;
        $this->request = $request;
        $this->aliases = $aliases;
        $this->formsHelper = $formsHelper;
        $this->seoCache = $seoCache;
        $this->config = $config;
        $this->seoModel = $seoModel;
    }

    /**
     * Returns the meta tags of the current page
     *
     * @return string
     */
    public function getMetaTags()
    {
        return [
            'description' => $this->request->getArea() === 'admin' ? '' : $this->getPageDescription(),
            'keywords' => $this->request->getArea() === 'admin' ? '' : $this->getPageKeywords(),
            'robots' => $this->request->getArea() === 'admin' ? 'noindex,nofollow' : $this->getPageRobotsSetting(),
            'previous_page' => $this->previousPage,
            'next_page' => $this->nextPage,
            'canonical' => $this->canonicalUrl,
        ];
    }

    /**
     * Returns the SEO description of the current page
     *
     * @return string
     */
    public function getPageDescription()
    {
        $description = $this->getDescription($this->request->getUriWithoutPages());
        if (empty($description)) {
            $description = $this->getDescription($this->request->getFullPath());
        }
        if (empty($description)) {
            $description = $this->getDescription($this->request->getModule());
        }
        if (empty($description)) {
            $description = $this->config->getSettings('seo')['meta_description'];
        }

        $postfix = '';
        if (!empty($description) && !empty($this->metaDescriptionPostfix)) {
            $postfix .= ' - ' . $this->metaDescriptionPostfix;
        }

        return $description . $postfix;
    }

    /**
     * Returns the SEO description of the given page
     *
     * @param string $path
     *
     * @return string
     */
    public function getDescription($path)
    {
        return $this->getSeoInformation($path, 'description');
    }

    /**
     * Returns the SEO keywords of the current page
     *
     * @return string
     */
    public function getPageKeywords()
    {
        $keywords = $this->getKeywords($this->request->getUriWithoutPages());
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->request->getFullPath());
        }
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->request->getModule());
        }

        return strtolower(!empty($keywords) ? $keywords : $this->config->getSettings('seo')['meta_keywords']);
    }

    /**
     * Returns the SEO keywords of the given page
     *
     * @param string $path
     *
     * @return string
     */
    public function getKeywords($path)
    {
        return $this->getSeoInformation($path, 'keywords');
    }

    /**
     * @param string $path
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    protected function getSeoInformation($path, $key, $defaultValue = '')
    {
        // Lazy load the cache
        if ($this->aliasesCache === null) {
            $this->aliasesCache = $this->seoCache->getCache();
        }

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliasesCache[$path][$key]) ? $this->aliasesCache[$path][$key] : $defaultValue;
    }

    /**
     * Returns the SEO robots setting for the current page
     *
     * @return string
     */
    public function getPageRobotsSetting()
    {
        $robots = $this->getRobotsSetting($this->request->getUriWithoutPages());
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->request->getFullPath());
        }
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->request->getModule());
        }

        return strtolower(!empty($robots) ? $robots : $this->getRobotsSetting());
    }

    /**
     * Returns the SEO robots settings for the given page
     *
     * @param string $path
     *
     * @return string
     */
    public function getRobotsSetting($path = '')
    {
        $replace = [
            1 => 'index,follow',
            2 => 'index,nofollow',
            3 => 'noindex,follow',
            4 => 'noindex,nofollow',
        ];

        if ($path === '') {
            return strtr($this->config->getSettings('seo')['robots'], $replace);
        } else {
            $robot = $this->getSeoInformation($path, 'robots', 0);

            if ($robot == 0) {
                $robot = $this->config->getSettings('seo')['robots'];
            }

            return strtr($robot, $replace);
        }
    }

    /**
     * Sets a SEO description postfix for te current page
     *
     * @param string $string
     *
     * @return $this
     */
    public function setDescriptionPostfix($string)
    {
        $this->metaDescriptionPostfix = $string;

        return $this;
    }

    /**
     * Sets the canonical URL for the current page
     *
     * @param string $path
     *
     * @return $this
     */
    public function setCanonicalUri($path)
    {
        $this->canonicalUrl = $path;

        return $this;
    }

    /**
     * Sets the next page (useful for pagination)
     *
     * @param string $path
     *
     * @return $this
     */
    public function setNextPage($path)
    {
        $this->nextPage = $path;

        return $this;
    }

    /**
     * Sets the previous page (useful for pagination)
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPreviousPage($path)
    {
        $this->previousPage = $path;

        return $this;
    }

    /**
     * Returns the SEO form fields
     *
     * @param string $path
     *
     * @return array
     */
    public function formFields($path = '')
    {
        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $alias = $this->request->getPost()->get('alias', $this->aliases->getUriAlias($path, true));
            $keywords = $this->request->getPost()->get('seo_keywords', $this->getKeywords($path));
            $description = $this->request->getPost()->get('seo_description', $this->getDescription($path));
            $robots = $this->getSeoInformation($path, 'robots', 0);
        } else {
            $alias = $keywords = $description = '';
            $robots = 0;
        }

        $langRobots = [
            sprintf($this->lang->t('seo', 'robots_use_system_default'), $this->getRobotsSetting()),
            $this->lang->t('seo', 'robots_index_follow'),
            $this->lang->t('seo', 'robots_index_nofollow'),
            $this->lang->t('seo', 'robots_noindex_follow'),
            $this->lang->t('seo', 'robots_noindex_nofollow')
        ];

        return [
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => $this->formsHelper->selectGenerator('seo_robots', [0, 1, 2, 3, 4], $langRobots, $robots)
        ];
    }

    /**
     * Deletes the given URL alias
     *
     * @param string $path
     *
     * @return boolean
     */
    public function deleteUriAlias($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        $bool = $this->seoModel->delete($path, 'uri');
        return $bool !== false && $this->seoCache->setCache() !== false;
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
     */
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';
        $keywords = Functions::strEncode($keywords);
        $description = Functions::strEncode($description);
        $values = [
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => (int)$robots
        ];

        // Update an existing result
        if ($this->seoModel->uriAliasExists($path) === true) {
            $bool = $this->seoModel->update($values, ['uri' => $path]);
        } else {
            $values['uri'] = $path;
            $bool = $this->seoModel->insert($values);
        }

        return $bool !== false && $this->seoCache->setCache() !== false;
    }
}
