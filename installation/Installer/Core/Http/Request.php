<?php
namespace ACP3\Installer\Core\Http;

use ACP3\Core;

/**
 * Class Request
 * @package ACP3\Installer\Core\Http
 */
class Request extends Core\Http\Request
{
    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param string                                 $defaultPath
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        $defaultPath = ''
    )
    {
        $this->appPath = $appPath;

        $this->fillParameterBags($_SERVER, $_POST, $_FILES, $_COOKIE);
        $this->setBaseUrl();

        $this->processQuery();

        // Set the user defined homepage of the website
        if ($this->query === '/' && $defaultPath !== '') {
            $this->query = $defaultPath;
        }

        $this->parseURI();
    }

    /**
     * @inheritdoc
     */
    protected function processQuery()
    {
        $this->setOriginalQuery();

        $this->query = $this->originalQuery;

        $this->area = 'install';

        return;
    }
}
