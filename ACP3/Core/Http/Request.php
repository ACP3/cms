<?php
namespace ACP3\Core\Http;

use ACP3\Core\Config;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\Request\ParameterBag;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Request
 * @package ACP3\Core\Http
 */
class Request extends AbstractRequest
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

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
     * @var \ACP3\Core\Http\Request\ParameterBag
     */
    protected $parameters;
    /**
     * @var string
     */
    protected $query = '';
    /**
     * @var string
     */
    protected $originalQuery = '';
    /**
     * @var bool
     */
    protected $isHomepage;

    /**
     * Request constructor.
     *
     * @param \ACP3\Core\Config                      $config
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Config $config,
        ApplicationPath $appPath
    )
    {
        $this->config = $config;
        $this->appPath = $appPath;

        parent::__construct();

        $this->processQuery();
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
    public function getOriginalQuery()
    {
        return $this->originalQuery;
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
    protected function processQuery()
    {
        $this->setOriginalQuery();

        $this->query = $this->originalQuery;

        // It's an request for the admin panel page
        if (preg_match(self::ADMIN_PANEL_PATTERN, $this->query)) {
            $this->area = AreaEnum::AREA_ADMIN;
            // strip "acp/"
            $this->query = substr($this->query, 4);
        } else {
            $this->area = AreaEnum::AREA_FRONTEND;

            $homepage = $this->config->getSettings('system')['homepage'];

            // Set the user defined homepage of the website
            if ($this->query === '/' && $homepage !== '') {
                $this->query = $homepage;
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
        if ($this->isHomepage === null) {
            $this->isHomepage = ($this->query === $this->config->getSettings('system')['homepage']);
        }

        return $this->isHomepage;
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

    protected function setOriginalQuery()
    {
        $this->originalQuery = substr(str_replace($this->appPath->getPhpSelf(), '',
            htmlentities($this->getServer()->get('PHP_SELF', ''), ENT_QUOTES)), 1);
        $this->originalQuery .= !preg_match('/\/$/', $this->originalQuery) ? '/' : '';
    }

    /**
     * @param array $query
     */
    protected function setRequestParameters(array $query)
    {
        $this->parameters = new ParameterBag([]);

        if (isset($query[3])) {
            $cQuery = count($query);

            for ($i = 3; $i < $cQuery; ++$i) {
                // Position
                if (preg_match('/^(page_(\d+))$/', $query[$i])) {
                    $this->parameters->add(['page' => (int)substr($query[$i], 5)]);
                } elseif (preg_match('/^(id_(\d+))$/', $query[$i])) { // ID eines Datensatzes
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
