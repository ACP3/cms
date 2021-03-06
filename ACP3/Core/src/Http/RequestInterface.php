<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface RequestInterface
{
    /**
     * @return SymfonyRequest
     */
    public function getSymfonyRequest();

    /**
     * Returns the used protocol of the current request.
     *
     * @return string
     */
    public function getScheme();

    /**
     * Returns the hostname of the current request.
     *
     * @return string
     */
    public function getHost();

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     *
     * @return string
     */
    public function getHttpHost();

    /**
     * @return string
     */
    public function getQuery();

    /**
     * Returns the original requested query.
     *
     * @return string
     */
    public function getPathInfo();

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
     * Returns the currently requested module, controller and controller action.
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Returns the currently requested module, controller and controller action without the area prefix.
     *
     * @return string
     */
    public function getFullPathWithoutArea();

    /**
     * Returns the currently requested module and controller.
     *
     * @return string
     */
    public function getModuleAndController();

    /**
     * Returns the currently requested module and controller without the area prefix.
     *
     * @return string
     */
    public function getModuleAndControllerWithoutArea();

    /**
     * @return bool
     */
    public function isHomepage();

    /**
     * Gibt die URI-Parameter aus.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getParameters();

    /**
     * Gibt die bereinigte URI-Query aus, d.h. ohne die anzuzeigende Seite.
     *
     * @return string
     */
    public function getUriWithoutPages();

    /**
     * @return bool
     */
    public function isXmlHttpRequest();

    /**
     * Returns the parameter bag of the $_COOKIE superglobal.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getCookies();

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal).
     *
     * @return \Symfony\Component\HttpFoundation\FileBag
     */
    public function getFiles();

    /**
     * Returns the parameter bag of the $_POST superglobal.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getPost();

    /**
     * Returns the parameter bag of the $_SERVER superglobal.
     *
     * @return \Symfony\Component\HttpFoundation\ServerBag
     */
    public function getServer();

    /**
     * @return \ACP3\Core\Http\Request\UserAgent
     */
    public function getUserAgent();

    /**
     * Processes the request.
     */
    public function processQuery();

    /**
     * @param string $homepage
     *
     * @return $this
     */
    public function setHomepage($homepage);

    public function setPathInfo(?string $pathInfo = null): void;
}
