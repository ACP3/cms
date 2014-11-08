<?php
namespace ACP3\Installer\Core;

/**
 * Class View
 * @package ACP3\Installer\Core
 */
class View extends \ACP3\Core\View
{
    /**
     * Gibt ein Template aus
     *
     * @param string $template
     * @param mixed $cacheId
     * @param mixed $compileId
     * @param object $parent
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

} 