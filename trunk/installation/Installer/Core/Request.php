<?php
namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Class Request
 * @package ACP3\Installer\Core
 */
class Request extends Core\Request
{
    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    public function __construct($defaultPath = '')
    {
        $this->preprocessUriQuery();

        // Set the user defined homepage of the website
        if ($this->query === '/' && $defaultPath !== '') {
            $this->query = $defaultPath;
        }

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

        $this->area = 'install';

        return;
    }

}