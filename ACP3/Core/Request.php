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
     * Array, welches die URI Parameter enthält
     *
     * @var array
     * @access protected
     */
    protected $params = array();
    /**
     * Die komplette übergebene URL
     *
     * @var string
     */
    public $query = '';

    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    function __construct()
    {
        $this->preprocessUriQuery();

        // Set the user defined homepage of the website
        if ($this->query === '/' && CONFIG_HOMEPAGE !== '') {
            $this->query = CONFIG_HOMEPAGE;
        }

        $this->setUriParameters();
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
        // Make it impossible to overwrite already set parameters
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
     * Setzt alle in URI::query enthaltenen Parameter
     *
     * @return void
     */
    protected function setUriParameters()
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