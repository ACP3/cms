<?php
namespace ACP3\Core\Assets;

/**
 * Interface MinifierInterface
 * @package ACP3\Core\Assets
 */
interface MinifierInterface
{
    /**
     * Returns the URI of the minified assets
     *
     * @param string $layout
     *
     * @return string
     */
    public function getURI($layout = 'layout');
}
