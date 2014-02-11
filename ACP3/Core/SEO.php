<?php
namespace ACP3\Core;

/**
 * Klasse zum Setzen von URI Aliases, Keywords und Beschreibungen für Seiten
 *
 * @author Tino Goratsch
 */
abstract class SEO
{
    /**
     * Caching Variable für die URI-Aliases
     *
     * @access private
     * @var array
     */
    protected static $aliases = array();
    /**
     * Gibt die nächste Seite an
     *
     * @var string
     */
    protected static $nextPage = '';
    /**
     * Gibt die vorherige Seite an
     *
     * @var string
     */
    protected static $previousPage = '';
    /**
     * Kanonische URL
     *
     * @var string
     */
    protected static $canonical = '';

    protected static $metaDescriptionPostfix = '';

    /**
     * Setzt den Cache für die URI-Aliase
     *
     * @return boolean
     */
    protected static function setSEOCache()
    {
        $aliases = Registry::get('Db')->fetchAll('SELECT uri, alias, keywords, description, robots FROM ' . DB_PRE . 'seo');
        $c_aliases = count($aliases);
        $data = array();

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = array(
                'alias' => $aliases[$i]['alias'],
                'keywords' => $aliases[$i]['keywords'],
                'description' => $aliases[$i]['description'],
                'robots' => $aliases[$i]['robots']
            );
        }

        return Cache::create('aliases', $data, 'seo');
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    protected static function getSEOCache()
    {
        if (Cache::check('aliases', 'seo') === false) {
            self::setSEOCache();
        }

        return Cache::output('aliases', 'seo');
    }

    /**
     * Gibt die für die jeweilige Seite gesetzten Metatags zurück
     *
     * @return string
     */
    public static function getMetaTags()
    {
        $meta = array(
            'description' => defined('IN_ADM') === true ? '' : self::getPageDescription(),
            'keywords' => defined('IN_ADM') === true ? '' : self::getPageKeywords(),
            'robots' => defined('IN_ADM') === true ? 'noindex,nofollow' : self::getPageRobotsSetting(),
            'previous_page' => self::$previousPage,
            'next_page' => self::$nextPage,
            'canonical' => self::$canonical,
        );
        Registry::get('View')->assign('meta', $meta);

        return Registry::get('View')->fetchTemplate('system/meta.tpl');
    }

    /**
     * Gibt die Beschreibung der aktuell angezeigten Seite zurück
     *
     * @return string
     */
    public static function getPageDescription()
    {
        // Meta Description für die Homepage einer Website
        if (Registry::get('URI')->query === CONFIG_HOMEPAGE) {
            return CONFIG_SEO_META_DESCRIPTION !== '' ? CONFIG_SEO_META_DESCRIPTION : '';
        } else {
            $description = self::getDescription(Registry::get('URI')->getUriWithoutPages());
            if (empty($description)) {
                $description = self::getDescription(Registry::get('URI')->mod . '/' . Registry::get('URI')->file);
            }

            return $description . (!empty($description) && !empty(self::$metaDescriptionPostfix) ? ' - ' . self::$metaDescriptionPostfix : '');
        }
    }

    /**
     * Gibt die Keywords der aktuell angezeigten Seite oder der
     * Elternseite zurück
     *
     * @return string
     */
    public static function getPageKeywords()
    {
        $keywords = self::getKeywords(Registry::get('URI')->getUriWithoutPages());
        if (empty($keywords)) {
            $keywords = self::getKeywords(Registry::get('URI')->mod . '/' . Registry::get('URI')->file);
        }
        if (empty($keywords)) {
            $keywords = self::getKeywords(Registry::get('URI')->mod);
        }

        return strtolower(!empty($keywords) ? $keywords : CONFIG_SEO_META_KEYWORDS);
    }

    /**
     * Gibt den Robots-Metatag der aktuell angezeigten Seite oder der
     * Elternseite zurück
     *
     * @return string
     */
    public static function getPageRobotsSetting()
    {
        $robots = self::getRobotsSetting(Registry::get('URI')->getUriWithoutPages());
        if (empty($robots)) {
            $robots = self::getRobotsSetting(Registry::get('URI')->mod . '/' . Registry::get('URI')->file);
        }
        if (empty($robots)) {
            $robots = self::getRobotsSetting(Registry::get('URI')->mod);
        }

        return strtolower(!empty($robots) ? $robots : self::getRobotsSetting());
    }

    /**
     * Gibt die Beschreibung der Seite zurück
     *
     * @param string $path
     * @return string
     */
    public static function getDescription($path)
    {
        self::_initCache();

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty(self::$aliases[$path]['description']) ? self::$aliases[$path]['description'] : '';
    }

    /**
     *
     * @param string $string
     */
    public static function setDescriptionPostfix($string)
    {
        self::$metaDescriptionPostfix = $string;
    }

    /**
     * Gibt die Schlüsselwörter der Seite zurück
     *
     * @param string $path
     * @return string
     */
    public static function getKeywords($path)
    {
        self::_initCache();

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty(self::$aliases[$path]['keywords']) ? self::$aliases[$path]['keywords'] : '';
    }

    /**
     * Gibt die jeweilige Einstellung für den Robots-Metatag zurück
     *
     * @param string $path
     * @return string
     */
    public static function getRobotsSetting($path = '')
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
            self::_initCache();

            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $robot = isset(self::$aliases[$path]) === false || self::$aliases[$path]['robots'] == 0 ? CONFIG_SEO_ROBOTS : self::$aliases[$path]['robots'];
            return strtr($robot, $replace);
        }
    }

    /**
     * Gibt einen URI-Alias zurück
     *
     * @param string $path
     * @param bool $for_form
     * @return string
     */
    public static function getUriAlias($path, $for_form = false)
    {
        self::_initCache();

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty(self::$aliases[$path]['alias']) ? self::$aliases[$path]['alias'] : ($for_form === true ? '' : $path);
    }

    /**
     * Setzt die kanonische URI
     *
     * @param string $path
     */
    public static function setCanonicalUri($path)
    {
        self::$canonical = $path;
    }

    /**
     * Setzt die nächste Seite
     *
     * @param string $path
     */
    public static function setNextPage($path)
    {
        self::$nextPage = $path;
    }

    /**
     * Setzt die vorherige Seite
     *
     * @param string $path
     */
    public static function setPreviousPage($path)
    {
        self::$previousPage = $path;
    }

    /**
     * Löscht einen URI-Alias
     *
     * @param string $path
     * @return boolean
     */
    public static function deleteUriAlias($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        $bool = Registry::get('Db')->delete(DB_PRE . 'seo', array('uri' => $path));
        $bool2 = self::setSEOCache();
        return $bool !== false && $bool2 !== false ? true : false;
    }

    /**
     * Trägt einen URI-Alias in die Datenbank ein bzw. aktualisiert den Eintrag
     *
     * @param string $path
     * @param string $alias
     * @param string $keywords
     * @param string $description
     * @param int $robots
     * @return boolean
     */
    public static function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';
        $keywords = Functions::strEncode($keywords);
        $description = Functions::strEncode($description);

        // Vorhandenen Alias aktualisieren
        if (Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'seo WHERE uri = ?', array($path)) == 1) {
            $bool = Registry::get('Db')->update(DB_PRE . 'seo', array('alias' => $alias, 'keywords' => $keywords, 'description' => $description, 'robots' => (int)$robots), array('uri' => $path));
            // Neuer Eintrag in DB
        } else {
            $bool = Registry::get('Db')->insert(DB_PRE . 'seo', array('alias' => $alias, 'uri' => $path, 'keywords' => $keywords, 'description' => $description, 'robots' => (int)$robots));
        }

        $bool2 = self::setSEOCache();
        return $bool !== false && $bool2 !== false ? true : false;
    }

    /**
     * Gibt die Formularfelder für die Suchmaschinenoptimierung aus
     *
     * @param string $path
     * @return string
     */
    public static function formFields($path = '')
    {
        if (!empty($path)) {
            $path .= !preg_match('/\/$/', $path) ? '/' : '';

            $alias = isset($_POST['alias']) ? $_POST['alias'] : self::getUriAlias($path, true);
            $keywords = isset($_POST['seo_keywords']) ? $_POST['seo_keywords'] : self::getKeywords($path);
            $description = isset($_POST['seo_description']) ? $_POST['seo_description'] : self::getDescription($path);
            $robots = isset(self::$aliases[$path]) === true ? self::$aliases[$path]['robots'] : 0;
        } else {
            $alias = $keywords = $description = '';
            $robots = 0;
        }

        $lang_robots = array(
            sprintf(Registry::get('Lang')->t('system', 'seo_robots_use_system_default'), self::getRobotsSetting()),
            Registry::get('Lang')->t('system', 'seo_robots_index_follow'),
            Registry::get('Lang')->t('system', 'seo_robots_index_nofollow'),
            Registry::get('Lang')->t('system', 'seo_robots_noindex_follow'),
            Registry::get('Lang')->t('system', 'seo_robots_noindex_nofollow')
        );
        $seo = array(
            'enable_uri_aliases' => (bool)CONFIG_SEO_ALIASES,
            'alias' => isset($alias) ? $alias : '',
            'keywords' => $keywords,
            'description' => $description,
            'robots' => Functions::selectGenerator('seo_robots', array(0, 1, 2, 3, 4), $lang_robots, $robots)
        );

        Registry::get('View')->assign('seo', $seo);
        return Registry::get('View')->fetchTemplate('system/seo_fields.tpl');
    }

    /**
     * Initialize the SEO Cache
     */
    private static function _initCache()
    {
        if (empty(self::$aliases)) {
            self::$aliases = self::getSEOCache();
        }
    }

    /**
     * Überprüft, ob ein URI-Alias existiert
     *
     * @param string $path
     * @return boolean
     */
    public static function uriAliasExists($path)
    {
        self::_initCache();

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return array_key_exists($path, self::$aliases) === true && !empty(self::$aliases[$path]['alias']);
    }
}