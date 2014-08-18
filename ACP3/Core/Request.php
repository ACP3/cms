<?php
namespace ACP3\Core;

use ACP3\Core\Router\Aliases;

/**
 * Class Request
 * @package ACP3\Core
 */
class Request
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';
    /**
     * Die komplette übergebene URL
     *
     * @var string
     */
    public $query = '';
    /**
     * @var string
     */
    public $originalQuery = '';
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * Array, welches die URI Parameter enthält
     *
     * @var array
     * @access protected
     */
    protected $params = array();

    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    public function __construct(\Doctrine\DBAL\Connection $db, Modules $modules)
    {
        $this->db = $db;
        $this->modules = $modules;

        $this->preprocessUriQuery();

        // Set the user defined homepage of the website
        if ($this->query === '/' && CONFIG_HOMEPAGE !== '') {
            $this->query = CONFIG_HOMEPAGE;
        }

        $this->checkForUriAlias();
        $this->setUriParameters();
    }

    /**
     * Grundlegende Verarbeitung der URI-Query
     */
    protected function preprocessUriQuery()
    {
        $this->originalQuery = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
        $this->originalQuery .= !preg_match('/\/$/', $this->originalQuery) ? '/' : '';

        $this->query = $this->originalQuery;

        // Definieren, dass man sich im Administrationsbereich befindet
        if (preg_match(self::ADMIN_PANEL_PATTERN, $this->query)) {
            $this->area = 'admin';
            // "acp/" entfernen
            $this->query = substr($this->query, 4);
        } else {
            $this->area = 'frontend';
        }

        return;
    }

    protected function checkForUriAlias()
    {
        // Nur ausführen, falls URI-Aliase aktiviert sind
        if ($this->area !== 'admin') {
            $probableQuery = $this->query;
            // Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
            if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)+(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
                $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
                // Keine entsprechende Module-Action gefunden -> muss Alias sein
                if ($this->modules->actionExists($this->area . '/' . $query[0] . '/' . $query[1]) === false) {
                    $length = 0;
                    foreach ($query as $row) {
                        if (strpos($row, '_') === false) {
                            $length += strlen($row) + 1;
                        } else {
                            break;
                        }
                    }
                    $params = substr($this->query, $length);
                    $probableQuery = substr($this->query, 0, $length);
                }
            }

            // Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
            $alias = $this->db->fetchColumn('SELECT uri FROM ' . DB_PRE . 'seo WHERE alias = ?', array(substr($probableQuery, 0, -1)));
            if (!empty($alias)) {
                $this->query = $alias . (!empty($params) ? $params : '');
            }
        }

        return;
    }

    /**
     * Setzt alle in URI::query enthaltenen Parameter
     *
     * @return void
     */
    public function setUriParameters()
    {
        $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($query[0])) {
            $this->mod = $query[0];
        } else {
            $this->mod = ($this->area === 'admin') ? 'acp' : 'news';
        }

        $this->controller = isset($query[1]) ? $query[1] : 'index';
        $this->file = isset($query[2]) ? $query[2] : 'index';

        if (isset($query[3])) {
            $c_query = count($query);

            for ($i = 3; $i < $c_query; ++$i) {
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
        }

        if (!isset($query[0])) {
            $this->query = $this->mod . '/';
        }
        if (!isset($query[1])) {
            $this->query .= $this->controller . '/';
        }
        if (!isset($query[2])) {
            $this->query .= $this->file . '/';
        }

        if (!empty($_POST['cat']) && is_numeric($_POST['cat']) === true) {
            $this->cat = (int)$_POST['cat'];
        }
        if (!empty($_POST['action'])) {
            $this->action = $_POST['action'];
        }

        return;
    }

    /**
     * Gibt einen URI Parameter aus
     *
     * @param string $key
     *
     * @return string|integer|null
     */
    public function __get($key)
    {
        return isset($this->params[$key]) === true ? $this->params[$key] : null;
    }

    /**
     * Setzt einen neuen URI Parameter
     *
     * @param string         $key
     * @param string|integer $value
     */
    public function __set($key, $value)
    {
        // Make it impossible to overwrite already set parameters
        if (isset($this->params[$key]) === false) {
            $this->params[$key] = $value;
        }
    }

    /**
     * Überprüft, ob ein URI-Parameter existiert
     *
     * @param string $key
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @return bool
     */
    public function getIsAjax()
    {
        if (isset($this->isAjax) === false) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->isAjax = true;
            }
        }

        return $this->isAjax;
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

}