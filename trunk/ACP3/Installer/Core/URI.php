<?php
namespace ACP3\Installer\Core;

/**
 * URI Router
 *
 * @author Tino Goratsch
 */
class URI extends \ACP3\Core\URI
{
    /**
     * Zerlegt u.a. die übergebenen Parameter in der URI in ihre Bestandteile
     */
    function __construct($defaultModule = '', $defaultFile = '')
    {
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
}