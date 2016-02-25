<?php
namespace ACP3\Core\Http;

/**
 * Interface RequestInterface
 * @package ACP3\Core\Http
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
     * Returns the protocol with the hostname
     *
     * @return string
     */
    public function getDomain();

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
    public function getAction();

    /**
     * Returns the currently requested module, controller and controller action
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Returns the currently requested module, controller and controller action without the area prefix
     *
     * @return string
     */
    public function getFullPathWithoutArea();

    /**
     * Returns the currently requested module and controller
     *
     * @return string
     */
    public function getModuleAndController();

    /**
     * Returns the currently requested module and controller without the area prefix
     *
     * @return string
     */
    public function getModuleAndControllerWithoutArea();

    /**
     * @return bool
     */
    public function isHomepage();

    /**
     * Gibt die URI-Parameter aus
     *
     * @return \ACP3\Core\Http\Request\ParameterBag
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
    public function isAjax();

    /**
     * Returns the parameter bag of the $_COOKIE superglobal
     *
     * @return \ACP3\Core\Http\Request\CookiesParameterBag
     */
    public function getCookies();

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal)
     *
     * @return \ACP3\Core\Http\Request\FilesParameterBag
     */
    public function getFiles();

    /**
     * Returns the parameter bag of the $_POST superglobal
     *
     * @return \ACP3\Core\Http\Request\ParameterBag
     */
    public function getPost();

    /**
     * Returns the parameter bag of the $_SERVER superglobal
     *
     * @return \Symfony\Component\HttpFoundation\ServerBag
     */
    public function getServer();

    /**
     * @return \ACP3\Core\Http\Request\UserAgent
     */
    public function getUserAgent();
}