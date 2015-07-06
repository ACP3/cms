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
     * @param string $defaultPath
     */
    public function __construct($defaultPath = '')
    {
        $this->_setBaseUrl();
        $this->processQuery();

        // Set the user defined homepage of the website
        if ($this->query === '/' && $defaultPath !== '') {
            $this->query = $defaultPath;
        }

        $this->setUriParameters();
    }

    /**
     * @inheritdoc
     */
    public function processQuery()
    {
        $this->setOriginalQuery();

        $this->query = $this->originalQuery;

        $this->area = 'install';

        return;
    }
}
