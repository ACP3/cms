<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Application;
use ACP3\Core\View\AbstractRenderer;

/**
 * Renderer for the Smarty template engine
 */
class Smarty extends AbstractRenderer
{

    /**
     *
     * @var \Smarty
     */
    public $renderer;

    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->renderer = new \Smarty();
        $this->renderer->error_reporting = defined('IN_INSTALL') === true || (defined('DEBUG') === true && DEBUG === true) ? E_ALL : 0;
        $this->renderer->compile_id = !empty($params['compile_id']) ? $params['compile_id'] : CONFIG_DESIGN;
        $this->renderer->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
        $this->renderer
            ->setTemplateDir(!empty($params['template_dir']) ? $params['template_dir'] : array(DESIGN_PATH_INTERNAL, MODULES_DIR))
            ->setCompileDir(CACHE_DIR . 'tpl_compiled/')
            ->setCacheDir(CACHE_DIR . 'tpl_cached/');


        if (!empty($params['plugins_dir'])) {
            $this->renderer->addPluginsDir($params['plugins_dir']);
        }

        $container = Application::getServiceContainer();

        $services = $container->getServiceIds();
        foreach ($services as $service) {
            if (strpos($service, 'smarty.plugin.') === 0) {
                $container->get($service)->registerPlugin($this->renderer);
            }
        }

        $this->renderer->registerClass('Validate', "\\ACP3\\Core\\Validate");
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->renderer->assign($name);
        } else {
            $this->renderer->assign($name, $value);
        }
    }

    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        return $this->renderer->fetch($template, $cache_id, $compile_id, $parent, $display);
    }

    public function display($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        echo $this->renderer->display($template, $cache_id, $compile_id, $parent);
    }

    public function templateExists($template)
    {
        return $this->renderer->templateExists($template);
    }

}
