<?php
namespace ACP3\Core\Http\Request;

/**
 * Class FilesParameterBag
 * @package ACP3\Core\Http\Request
 */
class FilesParameterBag extends ParameterBag
{
    /**
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->all()[$key] : [];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->all()) && !empty($this->all()[$key]['name']);
    }
}
