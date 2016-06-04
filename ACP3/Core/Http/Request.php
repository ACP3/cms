<?php
namespace ACP3\Core\Http;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules;

/**
 * Class Request
 * @package ACP3\Core\Http
 */
class Request extends AbstractRequest
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var string
     */
    protected $area = '';
    /**
     * @var string
     */
    protected $module = '';
    /**
     * @var string
     */
    protected $controller = '';
    /**
     * @var string
     */
    protected $action = '';
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;
    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $pathInfo = '';

    /**
     * Request constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $symfonyRequest
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $symfonyRequest)
    {
        parent::__construct($symfonyRequest);
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * @inheritdoc
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @inheritdoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @inheritdoc
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function getFullPath()
    {
        return $this->getModuleAndController() . $this->action . '/';
    }

    /**
     * @inheritdoc
     */
    public function getFullPathWithoutArea()
    {
        return $this->getModuleAndControllerWithoutArea() . $this->action . '/';
    }

    /**
     * @inheritdoc
     */
    public function getModuleAndController()
    {
        $path = ($this->area === AreaEnum::AREA_ADMIN) ? 'acp/' : '';
        $path .= $this->getModuleAndControllerWithoutArea();

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getModuleAndControllerWithoutArea()
    {
        return $this->module . '/' . $this->controller . '/';
    }

    /**
     * Processes the URL of the current request
     */
    public function processQuery()
    {
        $this->setPathInfo();

        $this->query = $this->pathInfo;

        // It's an request for the admin panel page
        if (preg_match(self::ADMIN_PANEL_PATTERN, $this->query)) {
            $this->area = AreaEnum::AREA_ADMIN;
            // strip "acp/"
            $this->query = substr($this->query, 4);
        } else {
            $this->area = AreaEnum::AREA_FRONTEND;

            // Set the user defined homepage of the website
            if ($this->query === '/' && $this->homepage !== '') {
                $this->query = $this->homepage;
            }
        }

        $this->parseURI();
    }

    /**
     * Setzt alle in URI::query enthaltenen Parameter
     */
    protected function parseURI()
    {
        $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($query[0])) {
            $this->module = $query[0];
        } else {
            $this->module = ($this->area === AreaEnum::AREA_ADMIN) ? 'acp' : 'news';
        }

        $this->controller = isset($query[1]) ? $query[1] : 'index';
        $this->action = isset($query[2]) ? $query[2] : 'index';

        $this->completeQuery($query);
        $this->setRequestParameters($query);
    }

    /**
     * @inheritdoc
     */
    public function isHomepage()
    {
        return ($this->query === $this->homepage);
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getUriWithoutPages()
    {
        return preg_replace('/\/page_(\d+)\//', '/', $this->query);
    }

    protected function setPathInfo()
    {
        $this->pathInfo = substr($this->symfonyRequest->getPathInfo(), 1);
        $this->pathInfo .= !preg_match('/\/$/', $this->pathInfo) ? '/' : '';
    }

    /**
     * @param array $query
     */
    protected function setRequestParameters(array $query)
    {
        $this->parameters = new \Symfony\Component\HttpFoundation\ParameterBag([]);

        if (isset($query[3])) {
            $cQuery = count($query);

            for ($i = 3; $i < $cQuery; ++$i) {
                if (preg_match('/^(page_(\d+))$/', $query[$i])) { // Current page
                    $this->parameters->add(['page' => (int)substr($query[$i], 5)]);
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // result ID
                    $this->parameters->add(['id' => (int)substr($query[$i], 3)]);
                } elseif (preg_match('/^(([a-z0-9-]+)_(.+))$/', $query[$i])) { // Additional URI parameters
                    $param = explode('_', $query[$i], 2);
                    $this->parameters->add([$param[0] => $param[1]]);
                }
            }
        }

        $this->parameters->set('cat', (int)$this->getPost()->get('cat', $this->parameters->get('cat')));
        $this->parameters->set('action', $this->getPost()->get('action', $this->parameters->get('action')));
    }

    /**
     * @param array $query
     */
    protected function completeQuery(array $query)
    {
        if (!isset($query[0])) {
            $this->query = $this->module . '/';
        }
        if (!isset($query[1])) {
            $this->query .= $this->controller . '/';
        }
        if (!isset($query[2])) {
            $this->query .= $this->action . '/';
        }
    }
}
