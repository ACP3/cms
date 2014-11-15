<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class HasPermission
 * @package ACP3\Core\View\Renderer\Smarty
 */
class HasPermission extends AbstractPlugin
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var string
     */
    protected $pluginName = 'has_permission';

    /**
     * @param Core\ACL $acl
     */
    public function __construct(Core\ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['path']) === true) {
            return $this->acl->hasPermission($params['path']);
        } else {
            return false;
        }
    }
}