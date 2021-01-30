<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Comments\Helpers;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\News\Installer\Schema as NewsSchema;
use ACP3\Modules\ACP3\Newscomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnNewsLayoutItemIndexAfterEventListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    private $commentsHelpers;

    public function __construct(
        Modules $modules,
        View $view,
        SettingsInterface $settings,
        Helpers $commentsHelpers)
    {
        $this->view = $view;
        $this->settings = $settings;
        $this->modules = $modules;
        $this->commentsHelpers = $commentsHelpers;
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isActive(CommentsSchema::MODULE_NAME) || !$this->modules->isActive(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);
        $news = $event->getParameters()['news'];

        if ($settings['comments'] == 1 && $news['comments'] == 1) {
            $news['comments_count'] = $this->commentsHelpers->commentsCount(
                NewsSchema::MODULE_NAME,
                $news['id']
            );
            $this->view->assign('news', $news);

            $event->addContent($this->view->fetchTemplate('Newscomments/Partials/news_layout_item_index_after.tpl'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'news.layout.item_index_after' => '__invoke',
        ];
    }
}
