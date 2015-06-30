<?php
namespace ACP3\Core\Assets;

/**
 * Interface MinifierInterface
 * @package ACP3\Core\Assets
 */
interface MinifierInterface
{
    /**
     * @param string $layout
     *
     * @return string
     */
    function getLink($layout = 'layout');
}