<?php
namespace ACP3\Core;

/**
 * URI Router
 *
 * @author Tino Goratsch
 */
class URI
{

    const PATTERN = '=^acp/=';

    /**
     * Array, welches die URI Parameter enthält
     *
     * @var array
     * @access protected
     */
    protected $params = array();
    /**
     * Caching Variable für die URI-Aliases
     *
     * @access private
     * @var array
     */
    protected $aliases = array();
    /**
     * Die komplette übergebene URL
     *
     * @var string
     */
    public $query = '';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var bool
     */
    protected $isAjax = false;

    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    function __construct(\Doctrine\DBAL\Connection $db, $defaultModule = '', $defaultFile = '')
    {
        $this->db = $db;

        $this->aliases = $this->getCache();

        // Minify von der URI-Verarbeitung ausschließen
        if ((bool)preg_match('=libraries/.+=', $_SERVER['PHP_SELF']) === false) {
            $this->preprocessUriQuery();
            if (defined('IN_INSTALL') === false) {
                // Query auf eine benutzerdefinierte Startseite setzen
                if ($this->query === '/' && CONFIG_HOMEPAGE !== '') {
                    $this->query = CONFIG_HOMEPAGE;
                }
                $this->checkForUriAlias();
            }

            $this->setUriParameters($defaultModule, $defaultFile);
        }
    }

    /**
     * Gibt einen URI Parameter aus
     *
     * @param string $key
     * @return string|integer|null
     */
    public function __get($key)
    {
        return isset($this->params[$key]) === true ? $this->params[$key] : null;
    }

    /**
     * Setzt einen neuen URI Parameter
     *
     * @param string $key
     * @param string|integer $value
     */
    public function __set($key, $value)
    {
        // Parameter sollten nicht überschrieben werden können
        if (isset($this->params[$key]) === false) {
            $this->params[$key] = $value;
        }
    }

    /**
     * Überprüft, ob ein URI-Parameter existiert
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * Grundlegende Verarbeitung der URI-Query
     */
    protected function preprocessUriQuery()
    {
        $this->query = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
        $this->query .= !preg_match('/\/$/', $this->query) ? '/' : '';

        if (preg_match(self::PATTERN, $this->query)) {
            // Definieren, dass man sich im Administrationsbereich befindet
            define('IN_ADM', true);
            // "acp/" entfernen
            $this->query = substr($this->query, 4);
        }

        return;
    }

    /**
     * @return bool
     */
    public function getIsAjax()
    {
        return $this->isAjax;
    }

    /**
     * Überprüft die URI auf einen möglichen URI-Alias und
     * macht im Erfolgsfall einen Redirect darauf
     *
     * @return void
     */
    protected function checkForUriAlias()
    {
        // Nur ausführen, falls URI-Aliase aktiviert sind
        if ((bool)CONFIG_SEO_ALIASES === true && !defined('IN_ADM')) {
            // Falls für Query ein Alias existiert, zu diesem weiterleiten
            if ($this->uriAliasExists($this->query) === true) {
                $this->redirect($this->query, 0, true); // URI-Alias wird von uri::route() erzeugt
            }

            $prob_query = $this->query;
            // Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
            if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)+(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
                $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
                // Keine entsprechende Module-Action gefunden -> muss Alias sein
                if (Modules::actionExists($query[0], $query[1]) === false) {
                    $length = 0;
                    foreach ($query as $row) {
                        if (strpos($row, '_') === false) {
                            $length += strlen($row) + 1;
                        } else {
                            break;
                        }
                    }
                    $params = substr($this->query, $length);
                    $prob_query = substr($this->query, 0, $length);
                }
            }

            // Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
            $alias = $this->db->fetchAssoc('SELECT uri FROM ' . DB_PRE . 'seo WHERE alias = ?', array(substr($prob_query, 0, -1)));
            if (!empty($alias)) {
                $this->query = $alias['uri'] . (!empty($params) ? $params : '');
            }
        }

        return;
    }

    /**
     * Setzt alle in URI::query enthaltenen Parameter
     *
     * @param string $defaultModule
     * @param string $defaultFile
     * @return void
     */
    protected function setUriParameters($defaultModule, $defaultFile)
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->isAjax = true;
        }

        $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($defaultModule) && empty($defaultFile)) {
            $defaultModule = defined('IN_ADM') ? 'acp' : 'news';
            $defaultFile = 'list';
        }

        $this->mod = isset($query[0]) ? $query[0] : $defaultModule;
        $this->file = (defined('IN_ADM') ? 'acp_' : '') . (isset($query[1]) ? $query[1] : $defaultFile);

        if (isset($query[2])) {
            $c_query = count($query);

            for ($i = 2; $i < $c_query; ++$i) {
                // Position
                if (preg_match('/^(page_(\d+))$/', $query[$i])) {
                    $this->page = (int)substr($query[$i], 5);
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // ID eines Datensatzes
                    $this->id = (int)substr($query[$i], 3);
                } elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) { // Additional URI parameters
                    $param = explode('_', $query[$i], 2);
                    $this->$param[0] = $param[1];
                }
            }
        } elseif (isset($query[0]) && !isset($query[1])) {
            // Workaround für Securitytoken-Generierung,
            // falls die URL nur aus dem Modulnamen besteht
            $this->query .= $defaultFile . '/';
        } elseif (!isset($query[0]) && !isset($query[1])) {
            $this->query = $defaultModule . '/' . $defaultFile . '/';
        }

        if (!empty($_POST['cat']) && Validate::isNumber($_POST['cat']) === true) {
            $this->cat = (int)$_POST['cat'];
        }
        if (!empty($_POST['action'])) {
            $this->action = $_POST['action'];
        }

        return;
    }

    /**
     * Gibt die URI-Parameter aus
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Gibt die bereinigte URI-Query aus, d.h. ohne die anzuzeigende Seite
     *
     * @return string
     */
    public function getUriWithoutPages()
    {
        return preg_replace('/\/page_(\d+)\//', '/', $this->query);
    }

    /**
     * Umleitung auf andere URLs
     *
     * @param string $args
     *  Leitet auf eine interne ACP3 Seite weiter
     * @param int|string $newPage
     *  Leitet auf eine externe Seite weiter
     * @param bool|int $movedPermanently
     */
    public function redirect($args, $newPage = '', $movedPermanently = false)
    {
        if (!empty($args)) {
            $protocol = empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) === 'off' ? 'http://' : 'https://';
            $host = $_SERVER['HTTP_HOST'];
            $url = $protocol . $host . $this->route($args);

            if ($this->isAjax === true) {
                $return = array(
                    'redirect_url' => $url
                );

                Functions::outputJson($return);
            } else {
                if ($movedPermanently === true) {
                    header('HTTP/1.1 301 Moved Permanently');
                }
                header('Location: ' . $url);
                exit;
            }
        }
        header('Location:' . str_replace('&amp;', '&', $newPage));
        exit;
    }

    /**
     * Generiert die ACP3 internen Hyperlinks
     *
     * @param $path
     * @param integer $alias
     *    Gibt an, ob für die auszugebende Seite der URI-Alias ausgegeben werden soll,
     *    falls dieser existiert
     * @internal param string $uri Inhalt der zu generierenden URL
     * @return string
     */
    public function route($path, $alias = 1)
    {
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

        if ((bool)CONFIG_SEO_ALIASES === true && !preg_match(self::PATTERN, $path)) {
            if (count(preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY)) === 1) {
                $path .= 'list/';
            }
            // Überprüfen, ob Alias vorhanden ist und diesen als URI verwenden
            if ($alias === 1) {
                $alias = $this->getUriAlias($path);
                $path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
            }
        }
        $prefix = (bool)CONFIG_SEO_MOD_REWRITE === false || preg_match(self::PATTERN, $path) ? PHP_SELF . '/' : (defined('IN_INSTALL') === true ? INSTALLER_ROOT_DIR : ROOT_DIR);
        return $prefix . $path;
    }

    /**
     * Gibt einen URI-Alias zurück
     *
     * @param string $path
     * @param bool $emptyIsNoResult
     * @return string
     */
    public function getUriAlias($path, $emptyIsNoResult = false)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return !empty($this->aliases[$path]['alias']) ? $this->aliases[$path]['alias'] : ($emptyIsNoResult === true ? '' : $path);
    }


    /**
     * Setzt den Cache für die URI-Aliase
     *
     * @return boolean
     */
    protected function setCache()
    {
        $aliases = $this->db->fetchAll('SELECT uri, alias FROM ' . DB_PRE . 'seo WHERE alias != ""');
        $c_aliases = count($aliases);
        $data = array();

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = array(
                'alias' => $aliases[$i]['alias'],
            );
        }

        return Cache::create('aliases', $data, 'uri');
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if (Cache::check('aliases', 'uri') === false) {
            $this->setCache();
        }

        return Cache::output('aliases', 'uri');
    }

    /**
     * Löscht einen URI-Alias
     *
     * @param string $path
     * @return boolean
     */
    public function deleteUriAlias($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        $bool = $this->db->delete(DB_PRE . 'seo', array('uri' => $path));
        return $bool !== false && $this->setCache() !== false;
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
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';
        $keywords = Functions::strEncode($keywords);
        $description = Functions::strEncode($description);
        $values = array(
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => (int)$robots
        );

        // Update an existing result
        if ($this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'seo WHERE uri = ?', array($path)) == 1) {
            $bool = $this->db->update(DB_PRE . 'seo', $values, array('uri' => $path));
        } else {
            $values['uri'] = $path;
            $bool = $this->db->insert(DB_PRE . 'seo', $values); // Neuer Eintrag in DB
        }

        return $bool !== false && $this->setCache() !== false;
    }

    /**
     * Überprüft, ob ein URI-Alias existiert
     *
     * @param string $path
     * @return boolean
     */
    public function uriAliasExists($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return isset($this->aliases[$path]) === true && !empty($this->aliases[$path]['alias']);
    }

}