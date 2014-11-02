<?php

namespace ACP3\Core;

use ACP3\Core\View\AbstractRenderer;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Klasse für die Ausgabe der Seite
 *
 * @author Tino Goratsch
 */
class View extends ContainerAware
{
    /**
     * @var AbstractRenderer
     */
    protected $renderer;

    /**
     * Gets the renderer
     *
     * @return object
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set the desired renderer with an optional config array
     *
     * @param string $renderer
     * @param array  $params
     *
     * @throws \Exception
     */
    public function setRenderer($renderer = 'smarty', array $params = array())
    {
        $serviceId = 'core.view.renderer.' . $renderer;
        if ($this->container->has($serviceId) === true) {
            $this->renderer = $this->container->get($serviceId);
            $this->renderer->configure($params);
        } else {
            throw new \Exception('Renderer ' . $renderer . ' not found!');
        }
    }

    /**
     * Gibt ein Template direkt aus
     *
     * @param string $template
     * @param mixed  $cacheId
     * @param null   $compileId
     * @param null   $parent
     *
     * @internal param int $cache_lifetime
     */
    public function displayTemplate($template, $cacheId = null, $compileId = null, $parent = null)
    {
        echo $this->fetchTemplate($template, $cacheId, $compileId, $parent, true);
    }

    /**
     * Gibt ein Template aus
     *
     * @param string  $template
     * @param mixed   $cacheId
     * @param mixed   $compileId
     * @param object  $parent
     * @param boolean $display
     *
     * @throws \Exception
     * @return string
     */
    public function fetchTemplate($template, $cacheId = null, $compileId = null, $parent = null, $display = false)
    {
        // If an template with directory is given, uppercase the first letter
        if (strpos($template, '/') !== false) {
            $template = ucfirst($template);
        }

        if ($this->templateExists($template)) {
            return $this->renderer->fetch($template, $cacheId, $compileId, $parent, $display);
        } else {
            // Pfad zerlegen
            $fragments = explode('/', $template);

            if (count($fragments) === 3) {
                $path = $fragments[0] . '/Resources/View/' . $fragments[1] . '/' . $fragments[2];
            } else {
                $path = $fragments[0] . '/Resources/View/' . $fragments[1];
            }

            if (count($fragments) > 1 && $this->templateExists($path)) {
                return $this->renderer->fetch($path, $cacheId, $compileId, $parent, $display);
            } else {
                throw new \Exception("The requested template " . $template . " can't be found!");
            }
        }
    }

    /**
     * Checks, whether a templates exists or not
     *
     * @param string $template
     *
     * @return boolean
     */
    public function templateExists($template)
    {
        return $this->renderer->templateExists($template);
    }

    /**
     * Weist dem View-Object eine Template-Variable zu
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return boolean
     */
    public function assign($name, $value = null)
    {
        return $this->renderer->assign($name, $value);
    }

}
