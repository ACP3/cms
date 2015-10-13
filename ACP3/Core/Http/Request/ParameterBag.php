<?php
namespace ACP3\Core\Http\Request;

/**
 * Class ParameterBag
 * @package ACP3\Core\Http\Request
 */
class ParameterBag extends \Symfony\Component\HttpFoundation\ParameterBag
{
   /**
     * @return int
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() === 0;
    }

}