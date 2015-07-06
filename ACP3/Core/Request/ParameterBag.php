<?php
namespace ACP3\Core\Request;

/**
 * Class ParameterBag
 * @package ACP3\Core\Request
 */
class ParameterBag
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->convertArrayToObject($data);
    }

    /**
     * @param array $data
     */
    private function convertArrayToObject(array $data)
    {
        $this->data = new\stdClass();
        foreach ($data as $key => $value) {
            $this->data->$key = $value;
        }
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data->$key : $default;
    }

    /**
     * @return \stdClass
     */
    public function getAllAsObject()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAllAsArray()
    {
        return (array)$this->data;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data->$key = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data->$key);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->getAllAsArray()) === 0;
    }

}