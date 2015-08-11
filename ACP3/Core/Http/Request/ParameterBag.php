<?php
namespace ACP3\Core\Http\Request;

/**
 * Class ParameterBag
 * @package ACP3\Core\Http\Request
 */
class ParameterBag
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function add($key, $value)
    {
        if ($this->has($key) === false) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

}