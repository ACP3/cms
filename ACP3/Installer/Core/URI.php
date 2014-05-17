<?php
namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * URI Router
 *
 * @author Tino Goratsch
 */
class URI
{

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
     * @var bool
     */
    protected $isAjax = false;

    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    function __construct($defaultModule = '', $defaultFile = '')
    {
        $this->preprocessUriQuery();
        $this->setUriParameters($defaultModule, $defaultFile);
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
     * @return bool
     */
    public function getIsAjax()
    {
        return $this->isAjax;
    }

    /**
     * Grundlegende Verarbeitung der URI-Query
     */
    protected function preprocessUriQuery()
    {
        $this->query = substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1);
        $this->query .= !preg_match('/\/$/', $this->query) ? '/' : '';
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

        $this->mod = isset($query[0]) ? $query[0] : $defaultModule;
        $this->file = isset($query[1]) ? $query[1] : $defaultFile;

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

        if (!empty($_POST['cat']) && Core\Validate::isNumber($_POST['cat']) === true) {
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

                Core\Functions::outputJson($return);
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
     * @return string
     */
    public function route($path)
    {
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

        $prefix = PHP_SELF . '/';
        return $prefix . $path;
    }

}