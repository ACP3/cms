<?php
namespace ACP3\Core\View\Renderer\Smarty\Resources;

/**
 * Class AbstractResource
 * @package ACP3\Core\View\Renderer\Smarty\Resource
 */
abstract class AbstractResource extends \Smarty_Resource_Custom
{
    /**
     * @var string
     */
    protected $resourceName = '';

    /**
     * @throws \SmartyException
     */
    public function registerResource(\Smarty $smarty)
    {
        $smarty->registerResource($this->resourceName, $this);
    }
}