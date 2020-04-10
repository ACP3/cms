<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;

class Helpers
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    private $emoticonsCache;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(
        Core\View $view,
        Cache $emoticonsCache
    ) {
        $this->view = $view;
        $this->emoticonsCache = $emoticonsCache;
    }

    /**
     * Erzeugt eine Auflistung der Emoticons.
     *
     * @param string $formFieldId Die ID des Eingabefeldes, in welches die Emoticons eingefügt werden sollen
     */
    public function emoticonsList(string $formFieldId = ''): string
    {
        $this->view->assign('emoticons_field_id', empty($formFieldId) ? 'message' : $formFieldId);
        $this->view->assign('emoticons', $this->emoticonsCache->getCache());

        return $this->view->fetchTemplate('Emoticons/Partials/emoticons.tpl');
    }

    /**
     * Ersetzt bestimmte Zeichen durch Emoticons.
     *
     * @param string $string Zu durchsuchender Text nach Zeichen
     */
    public function emoticonsReplace(string $string): string
    {
        return \strtr($string, $this->emoticonsCache->getCache());
    }
}
