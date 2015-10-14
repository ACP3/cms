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
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Core\View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }
}