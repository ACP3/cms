<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;

class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    protected $emoticonsCache;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var bool
     */
    private $isActive = false;

    /**
     * @param \ACP3\Core\View                    $view
     * @param Core\Modules                       $modules
     * @param \ACP3\Modules\ACP3\Emoticons\Cache $emoticonsCache
     */
    public function __construct(
        Core\View $view,
        Core\Modules $modules,
        Cache $emoticonsCache
    ) {
        $this->view = $view;
        $this->emoticonsCache = $emoticonsCache;
        $this->isActive = $modules->isActive(Schema::MODULE_NAME);
    }

    /**
     * Erzeugt eine Auflistung der Emoticons.
     *
     * @param string $formFieldId
     *                            Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
     *
     * @return string
     */
    public function emoticonsList($formFieldId = '')
    {
        if ($this->isActive) {
            $this->view->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
            $this->view->assign('emoticons', $this->emoticonsCache->getCache());

            return $this->view->fetchTemplate('Emoticons/Partials/emoticons.tpl');
        }

        return '';
    }

    /**
     * Ersetzt bestimmte Zeichen durch Emoticons.
     *
     * @param string $string
     *                       Zu durchsuchender Text nach Zeichen
     *
     * @return string
     */
    public function emoticonsReplace($string)
    {
        if ($this->isActive) {
            return \strtr($string, $this->emoticonsCache->getCache());
        }

        return $string;
    }
}
