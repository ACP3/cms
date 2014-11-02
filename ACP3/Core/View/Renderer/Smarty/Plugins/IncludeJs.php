<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class IncludeJs
 * @package ACP3\Core\View\Renderer\Smarty
 */
class IncludeJs extends AbstractPlugin
{
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var array
     */
    protected $alreadyIncluded = array();
    /**
     * @var string
     */
    protected $pluginName = 'include_js';

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
        if (isset($params['module'], $params['file']) === true &&
            (bool)preg_match('=/=', $params['module']) === false &&
            (bool)preg_match('=\./=', $params['file']) === false
        ) {
            // Do not include the same file multiple times
            $key = $params['module'] . '/' . $params['file'];
            if (isset($alreadyIncluded[$key]) === false) {
                if (!empty($params['depends'])) {
                    $this->view->enableJsLibraries(explode(',', $params['depends']));
                }

                $this->alreadyIncluded[$key] = true;

                $script = '<script type="text/javascript" src="%s"></script>';
                $module = ucfirst($params['module']);
                $file = $params['file'];

                if (is_file(DESIGN_PATH_INTERNAL . $module . '/js/' . $file . '.js') === true) {
                    return sprintf($script, DESIGN_PATH . $module . '/jsj/' . $file . '.js');
                } elseif (is_file(MODULES_DIR . $module . '/Resources/Assets/js/' . $file . '.js') === true) {
                    return sprintf($script, ROOT_DIR . 'ACP3/Modules/' . $module . '/Resources/Assets/js/' . $file . '.js');
                }
            }
            return '';
        }

        throw new \Exception('Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!');
    }
}