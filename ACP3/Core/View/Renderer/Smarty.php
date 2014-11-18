<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\View\AbstractRenderer;
use ACP3\Core\View\Renderer\Smarty\Blocks\AbstractBlock;
use ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilter;
use ACP3\Core\View\Renderer\Smarty\Modifiers\AbstractModifier;
use ACP3\Core\View\Renderer\Smarty\Plugins\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\Resources\AbstractResource;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty extends AbstractRenderer
{

    /**
     * @var \Smarty
     */
    public $renderer;

    /**
     * @param array $params
     * @throws \SmartyException
     */
    public function configure(array $params = array())
    {
        $this->renderer = new \Smarty();
        $this->renderer->error_reporting = defined('IN_INSTALL') === true || (defined('DEBUG') === true && DEBUG === true) ? E_ALL : 0;
        $this->renderer->compile_id = !empty($params['compile_id']) ? $params['compile_id'] : CONFIG_DESIGN;
        $this->renderer->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
        $this->renderer
            ->setCompileDir(CACHE_DIR . 'tpl_compiled/')
            ->setCacheDir(CACHE_DIR . 'tpl_cached/');

        $services = $this->container->getServiceIds();
        foreach ($services as $serviceName) {
            if (strpos($serviceName, 'smarty.plugin.') === 0) {
                /** @var AbstractPlugin $plugin */
                $plugin = $this->container->get($serviceName);
                $plugin->registerPlugin($this->renderer);
            } elseif (strpos($serviceName, 'smarty.block.') === 0) {
                /** @var AbstractBlock $block */
                $block = $this->container->get($serviceName);
                $block->registerBlock($this->renderer);
            } elseif (strpos($serviceName, 'smarty.filter.') === 0) {
                /** @var AbstractFilter $filter */
                $filter = $this->container->get($serviceName);
                $filter->registerFilter($this->renderer);
            } elseif (strpos($serviceName, 'smarty.modifier.') === 0) {
                /** @var AbstractModifier $modifier */
                $modifier = $this->container->get($serviceName);
                $modifier->registerModifier($this->renderer);
            } elseif (strpos($serviceName, 'smarty.resource.') === 0) {
                /** @var AbstractResource $resource */
                $resource = $this->container->get($serviceName);
                $resource->registerResource($this->renderer);
            }
        }

        $this->renderer->registerClass('Misc', "\\ACP3\\Core\\Validator\\Rules\\Misc");
    }

    /**
     * @param      $name
     * @param null $value
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->renderer->assign($name);
        } else {
            $this->renderer->assign($name, $value);
        }
    }

    /**
     * @param      $template
     * @param null $cache_id
     * @param null $compile_id
     * @param null $parent
     * @param bool $display
     *
     * @return bool|mixed|string
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        return $this->renderer->fetch($template, $cache_id, $compile_id, $parent, $display);
    }

    public function display($template, $cache_id = null, $compile_id = null, $parent = null)
    {
        $this->renderer->display($template, $cache_id, $compile_id, $parent);
    }

    /**
     * @param $template
     *
     * @return bool
     */
    public function templateExists($template)
    {
        return $this->renderer->templateExists($template);
    }

}