<?php
namespace ACP3\Modules\ACP3\Feeds\Event;

use ACP3\Core\View;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DisplayFeed
 * @package ACP3\Modules\ACP3\Feeds\Event
 */
class DisplayFeed extends Event
{
    /**
     * @var string
     */
    protected $moduleName;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Core\View $view
     * @param string          $moduleName
     */
    public function __construct(View $view, $moduleName)
    {
        $this->view = $view;
        $this->moduleName = strtolower($moduleName);
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }
}