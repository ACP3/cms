<?php
namespace ACP3\Modules\ACP3\Menus\Installer;

use ACP3\Core\Modules\Installer\AbstractSampleData;

/**
 * Class SampleData
 * @package ACP3\Modules\ACP3\Menus\Installer
 */
class SampleData extends AbstractSampleData
{
    /**
     * @return array
     */
    public function sampleData()
    {
        $translator = $this->schemaHelper->getContainer()->get('core.lang');

        return [
            "INSERT INTO `{pre}menus` VALUES (1, 'main', '{$translator->t('install', 'pages_main')}');",
            "INSERT INTO `{pre}menus` VALUES (2, 'sidebar', '{$translator->t('install', 'pages_sidebar')}');",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 1, 0, 1, 4, 1, '{$translator->t('install', 'pages_news')}', 'news', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 1, 1, 2, 3, 1, '{$translator->t('install', 'pages_newsletter')}', 'newsletter', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 3, 0, 5, 6, 1, '{$translator->t('install', 'pages_files')}', 'files', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 4, 0, 7, 8, 1, '{$translator->t('install', 'pages_gallery')}', 'gallery', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 5, 0, 9, 10, 1, '{$translator->t('install', 'pages_guestbook')}', 'guestbook', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 6, 0, 11, 12, 1, '{$translator->t('install', 'pages_polls')}', 'polls', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 1, 7, 0, 13, 14, 1, '{$translator->t('install', 'pages_search')}', 'search', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 1, 2, 8, 0, 15, 16, 1, '{$translator->t('install', 'pages_contact')}', 'contact', 1);",
            "INSERT INTO `{pre}menu_items` VALUES ('', 2, 2, 9, 0, 17, 18, 1, '{$translator->t('install', 'pages_imprint')}', 'contact/index/imprint/', 1);",
        ];
    }
}
