<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Controller\AreaEnum;

class Request extends AbstractRequest
{
    private const ADMIN_PANEL_PATTERN = 'acp/';
    private const WIDGET_PATTERN = 'widget/';
    private const FRONTEND_PATTERN = 'frontend/';

    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $pathInfo = '';

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->getSymfonyRequest()->attributes->get('_area');
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        return $this->getSymfonyRequest()->attributes->get('_module');
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->getSymfonyRequest()->attributes->get('_controller');
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getSymfonyRequest()->attributes->get('_controllerAction');
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPath()
    {
        return $this->getModuleAndController() . $this->getAction() . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getFullPathWithoutArea()
    {
        return $this->getModuleAndControllerWithoutArea() . $this->getAction() . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleAndController()
    {
        $path = ($this->getArea() === AreaEnum::AREA_ADMIN) ? 'acp/' : '';

        return $path . $this->getModuleAndControllerWithoutArea();
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleAndControllerWithoutArea()
    {
        return $this->getModule() . '/' . $this->getController() . '/';
    }

    /**
     * Processes the URL of the current request.
     */
    public function processQuery()
    {
        $this->query = $this->pathInfo;

        // It's an request for the admin panel page
        if (str_starts_with($this->query, self::ADMIN_PANEL_PATTERN)) {
            $this->getSymfonyRequest()->attributes->set('_area', AreaEnum::AREA_ADMIN);
            // strip "acp/"
            $this->query = substr($this->query, \strlen(self::ADMIN_PANEL_PATTERN));
        } elseif (str_starts_with($this->query, self::WIDGET_PATTERN)) {
            $this->getSymfonyRequest()->attributes->set('_area', AreaEnum::AREA_WIDGET);

            // strip "widget/"
            $this->query = substr($this->query, \strlen(self::WIDGET_PATTERN));
        } else {
            if (str_starts_with($this->query, self::FRONTEND_PATTERN)) {
                $this->query = substr($this->query, \strlen(self::FRONTEND_PATTERN));
            }

            $this->getSymfonyRequest()->attributes->set('_area', AreaEnum::AREA_FRONTEND);

            // Set the user defined homepage of the website
            if ($this->query === '/' && $this->homepage !== '') {
                $this->query = $this->homepage;
            }
        }

        $this->parseURI();
    }

    /**
     * Setzt alle in URI::query enthaltenen Parameter.
     */
    protected function parseURI()
    {
        $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($query[0])) {
            $this->getSymfonyRequest()->attributes->set('_module', $query[0]);
        } else {
            $this->getSymfonyRequest()->attributes->set(
                '_module',
                ($this->getArea() === AreaEnum::AREA_ADMIN) ? 'acp' : 'news'
            );
        }

        $this->getSymfonyRequest()->attributes->set(
            '_controller',
            $query[1] ?? 'index'
        );
        $this->getSymfonyRequest()->attributes->set(
            '_controllerAction',
            $query[2] ?? 'index'
        );

        $this->completeQuery($query);
        $this->setRequestParameters($query);
    }

    /**
     * {@inheritdoc}
     */
    public function isHomepage()
    {
        return ($this->query === $this->homepage) && $this->getArea() === AreaEnum::AREA_FRONTEND;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->getSymfonyRequest()->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getUriWithoutPages()
    {
        return preg_replace('/\/page_(\d+)\//', '/', $this->query);
    }

    /**
     * {@inheritdoc}
     */
    public function setPathInfo(?string $pathInfo = null): void
    {
        if ($pathInfo !== null) {
            $this->pathInfo = $pathInfo;
        } else {
            $this->pathInfo = substr($this->getSymfonyRequest()->getPathInfo(), 1);
        }

        $this->pathInfo .= !preg_match('/\/$/', $this->pathInfo) ? '/' : '';
    }

    protected function setRequestParameters(array $query)
    {
        if (isset($query[3])) {
            $cQuery = \count($query);

            for ($i = 3; $i < $cQuery; ++$i) {
                if (preg_match('/^(page_(\d+))$/', $query[$i])) { // Current page
                    $this->getSymfonyRequest()->attributes->add(['page' => (int) substr($query[$i], 5)]);
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // result ID
                    $this->getSymfonyRequest()->attributes->add(['id' => (int) substr($query[$i], 3)]);
                } elseif (preg_match('/^(([a-zA-Z0-9\-]+)_(.+))$/', $query[$i])) { // Additional URI parameters
                    $param = explode('_', $query[$i], 2);
                    $this->getSymfonyRequest()->attributes->add([$param[0] => $param[1]]);
                }
            }
        }

        $this->getSymfonyRequest()->attributes->set(
            'cat',
            (int) $this->getPost()->get('cat', $this->getSymfonyRequest()->attributes->get('cat'))
        );
        $this->getSymfonyRequest()->attributes->set(
            'action',
            $this->getPost()->get('action', $this->getSymfonyRequest()->attributes->get('action'))
        );
    }

    protected function completeQuery(array $query)
    {
        if (!isset($query[0])) {
            $this->query = $this->getModule() . '/';
        }
        if (!isset($query[1])) {
            $this->query .= $this->getController() . '/';
        }
        if (!isset($query[2])) {
            $this->query .= $this->getAction() . '/';
        }
    }
}
