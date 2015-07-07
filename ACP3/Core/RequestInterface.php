<?php
namespace ACP3\Core;

use ACP3\Core\Request\ParameterBag;

/**
 * Interface RequestInterface
 * @package ACP3\Core
 */
interface RequestInterface
{
    /**
     * Returns the used protocol of the current request
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Returns the hostname of the current request
     *
     * @return string
     */
    public function getHostname();

    /**
     * @return string
     */
    public function getQuery();

    /**
     * Returns the original requested query
     *
     * @return string
     */
    public function getOriginalQuery();

    /**
     * @return string
     */
    public function getArea();

    /**
     * @return string
     */
    public function getModule();

    /**
     * @return string
     */
    public function getController();

    /**
     * @return string
     */
    public function getControllerAction();

    /**
     * Returns the currently requested module, controller and controller action
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Returns the currently requested module and controller
     *
     * @return string
     */
    public function getModuleAndController();

    /**
     * @return bool
     */
    public function getIsHomepage();

    /**
     * Gibt zurück, ob der aktuelle User Agent ein mobiler Browser ist, oder nicht.
     *
     * @return boolean
     * @see http://detectmobilebrowsers.com/download/php
     */
    public function isMobileBrowser();

    /**
     * Gibt die URI-Parameter aus
     *
     * @return array
     */
    public function getParameters();

    /**
     * Gibt die bereinigte URI-Query aus, d.h. ohne die anzuzeigende Seite
     *
     * @return string
     */
    public function getUriWithoutPages();

    /**
     * @return bool
     */
    public function getIsAjax();

    /**
     * Returns the parameter bag of the $_COOKIE superglobal
     *
     * @return ParameterBag
     */
    public function getCookie();

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal)
     *
     * @return \ACP3\Core\Request\ParameterBag
     */
    public function getFiles();

    /**
     * Returns the parameter bag of the $_POST superglobal
     *
     * @return ParameterBag
     */
    public function getPost();

    /**
     * Returns the parameter bag of the $_SERVER superglobal
     *
     * @return \ACP3\Core\Request\ParameterBag
     */
    public function getServer();
}