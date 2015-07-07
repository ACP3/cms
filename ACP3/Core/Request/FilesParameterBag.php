<?php
namespace ACP3\Core\Request;

/**
 * Class FilesParameterBag
 * @package ACP3\Core\Request
 */
class FilesParameterBag extends ParameterBag
{
    /**
     * @param string $key
     * @param array  $default
     *
     * @return mixed
     */
    public function get($key, $default = [])
    {
        return $this->has($key) ? $this->getAll()[$key] : $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->getAll()) && !empty($this->getAll()[$key]['name']);
    }
}