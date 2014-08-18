<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;

/**
 * Class Mark
 * @package ACP3\Core\View\Renderer\Smarty
 */
class Mark extends AbstractPlugin
{
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var array
     */
    protected $initialized = false;
    /**
     * @var string
     */
    protected $pluginName = 'mark';

    public function __construct(Core\View $view)
    {
        $this->view = $view;
    }

    /**
     * @param $params
     *
     * @throws \Exception
     * @return string
     */
    public function process($params)
    {
        $this->view->assign('checkbox_name', $params['name']);
        $this->view->assign('mark_all_id', !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all');
        $this->view->assign('is_initialized', $this->initialized);

        $this->initialized = true;

        return $this->view->fetchTemplate('system/mark.tpl');
    }
}