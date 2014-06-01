<?php
namespace ACP3\Core;

/**
 * Klasse zum Setzen von URI Aliases, Keywords und Beschreibungen für Seiten
 *
 * @author Tino Goratsch
 */
class SEO
{
    /**
     * @var Lang
     */
    protected $db;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var URI
     */
    protected $uri;
    /**
     * @var View
     */
    protected $view;
    /**
     * Gibt die nächste Seite an
     *
     * @var string
     */
    protected $nextPage = '';
    /**
     * Gibt die vorherige Seite an
     *
     * @var string
     */
    protected $previousPage = '';
    /**
     * Kanonische URL
     *
     * @var string
     */
    protected $canonical = '';
    /**
     * @var array
     */
    protected $aliases = array();

    protected $metaDescriptionPostfix = '';

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Lang $lang,
        URI $uri,
        View $view)
    {
        $this->db = $db;
        $this->lang = $lang;
        $this->uri = $uri;
        $this->view = $view;

        $this->aliases = $this->getCache();
    }

    /**
     * Setzt den Cache für die URI-Aliase
     *
     * @return boolean
     */
    public function setCache()
    {
        $aliases = $this->db->fetchAll('SELECT uri, keywords, description, robots FROM ' . DB_PRE . 'seo WHERE keywords != "" OR description != "" OR robots != 0');
        $c_aliases = count($aliases);
        $data = array();

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = array(
                'keywords' => $aliases[$i]['keywords'],
                'description' => $aliases[$i]['description'],
                'robots' => $aliases[$i]['robots']
            );
        }

        return Cache::create('meta', $data, 'seo');
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if (Cache::check('meta', 'seo') === false) {
            $this->setCache();
        }

        return Cache::output('meta', 'seo');
    }

    /**
     * Gibt die für die jeweilige Seite gesetzten Metatags zurück
     *
     * @return string
     */
    public function getMetaTags()
    {
        $meta = array(
            'description' => $this->uri->area === 'admin' ? '' : $this->getPageDescription(),
            'keywords' => $this->uri->area === 'admin' ? '' : $this->getPageKeywords(),
            'robots' => $this->uri->area === 'admin' ? 'noindex,nofollow' : $this->getPageRobotsSetting(),
            'previous_page' => $this->previousPage,
            'next_page' => $this->nextPage,
            'canonical' => $this->canonical,
        );
        $this->view->assign('meta', $meta);

        return $this->view->fetchTemplate('system/meta.tpl');
    }

    /**
     * Gibt die Beschreibung der aktuell angezeigten Seite zurück
     *
     * @return string
     */
    public function getPageDescription()
    {
        // Meta Description für die Homepage einer Website
        if ($this->uri->query === CONFIG_HOMEPAGE) {
            return CONFIG_SEO_META_DESCRIPTION !== '' ? CONFIG_SEO_META_DESCRIPTION : '';
        } else {
            $description = $this->getDescription($this->uri->getUriWithoutPages());
            if (empty($description)) {
                $description = $this->getDescription($this->uri->mod . '/' . $this->uri->controller . '/' . $this->uri->file);
            }

            return $description . (!empty($description) && !empty($this->metaDescriptionPostfix) ? ' - ' . $this->metaDescriptionPostfix : '');
        }
    }

    /**
     * Gibt die Keywords der aktuell angezeigten Seite oder der
     * Elternseite zurück
     *
     * @return string
     */
    public function getPageKeywords()
    {
        $keywords = $this->getKeywords($this->uri->getUriWithoutPages());
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->uri->mod . '/' . $this->uri->controller . '/' . $this->uri->file);
        }
        if (empty($keywords)) {
            $keywords = $this->getKeywords($this->uri->mod);
        }

        return strtolower(!empty($keywords) ? $keywords : CONFIG_SEO_META_KEYWORDS);
    }

    /**
     * Gibt den Robots-Metatag der aktuell angezeigten Seite oder der
     * Elternseite zurück
     *
     * @return string
     */
    public function getPageRobotsSetting()
    {
        $robots = $this->getRobotsSetting($this->uri->getUriWithoutPages());
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->uri->mod . '/' . $this->uri->controller . '/' . $this->uri->file);
        }
        if (empty($robots)) {
            $robots = $this->getRobotsSetting($this->uri->mod);
        }

        return strtolower(!empty($robots) ? $robots : $this->getRobotsSetting());
    }

    /**
     * Gibt die Beschreibung der Seite zurück
     *
     * @param string $path
     * @return string
     */
    public function getDescription($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliases[$path]['description']) ? $this->aliases[$path]['description'] : '';
    }

    /**
     *
     * @param string $string
     */
    public function setDescriptionPostfix($string)
    {
        $this->metaDescriptionPostfix = $string;
    }

    /**
     * Gibt die Schlüsselwörter der Seite zurück
     *
     * @param string $path
     * @return string
     */
    public function getKeywords($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliases[$path]['keywords']) ? $this->aliases[$path]['keywords'] : '';
    }

    /**
     * Gibt die jeweilige Einstellung für den Robots-Metatag zurück
     *
     * @param string $path
     * @return string
     */
    public function getRobotsSetting($path = '')
    {
        $replace = array(
            1 => 'index,follow',
            2 => 'index,nofollow',
            3 => 'noindex,follow',
            4 => 'noindex,nofollow',
        );

        if ($path === '') {
            return strtr(CONFIG_SEO_ROBOTS, $replace);
        } else {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $robot = isset($this->aliases[$path]) === false || $this->aliases[$path]['robots'] == 0 ? CONFIG_SEO_ROBOTS : $this->aliases[$path]['robots'];
            return strtr($robot, $replace);
        }
    }

    /**
     * Setzt die kanonische URI
     *
     * @param string $path
     */
    public function setCanonicalUri($path)
    {
        $this->canonical = $path;
    }

    /**
     * Setzt die nächste Seite
     *
     * @param string $path
     */
    public function setNextPage($path)
    {
        $this->nextPage = $path;
    }

    /**
     * Setzt die vorherige Seite
     *
     * @param string $path
     */
    public function setPreviousPage($path)
    {
        $this->previousPage = $path;
    }

    /**
     * Gibt die Formularfelder für die Suchmaschinenoptimierung aus
     *
     * @param string $path
     * @return string
     */
    public function formFields($path = '')
    {
        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $alias = isset($_POST['alias']) ? $_POST['alias'] : $this->uri->getUriAlias($path, true);
            $keywords = isset($_POST['seo_keywords']) ? $_POST['seo_keywords'] : $this->getKeywords($path);
            $description = isset($_POST['seo_description']) ? $_POST['seo_description'] : $this->getDescription($path);
            $robots = isset($this->aliases[$path]) === true ? $this->aliases[$path]['robots'] : 0;
        } else {
            $alias = $keywords = $description = '';
            $robots = 0;
        }

        $langRobots = array(
            sprintf($this->lang->t('system', 'seo_robots_use_system_default'), $this->getRobotsSetting()),
            $this->lang->t('system', 'seo_robots_index_follow'),
            $this->lang->t('system', 'seo_robots_index_nofollow'),
            $this->lang->t('system', 'seo_robots_noindex_follow'),
            $this->lang->t('system', 'seo_robots_noindex_nofollow')
        );
        $seo = array(
            'enable_uri_aliases' => (bool)CONFIG_SEO_ALIASES,
            'alias' => isset($alias) ? $alias : '',
            'keywords' => $keywords,
            'description' => $description,
            'robots' => Functions::selectGenerator('seo_robots', array(0, 1, 2, 3, 4), $langRobots, $robots)
        );

        $this->view->assign('seo', $seo);
        return $this->view->fetchTemplate('system/seo_fields.tpl');
    }

}