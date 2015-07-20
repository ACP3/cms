<?php
namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\Modules\Installer\AbstractSampleData;

/**
 * Class SampleData
 * @package ACP3\Modules\ACP3\News\Installer
 */
class SampleData extends AbstractSampleData
{

    /**
     * @return array
     */
    public function sampleData()
    {
        $currentDate = gmdate('Y-m-d H:i:s');
        $lang = $this->schemaHelper->getContainer()->get('core.lang');

        return [
            "INSERT INTO `{pre}categories` VALUES ('', '{$lang->t('install', 'category_name')}', '', '{$lang->t('install', 'category_description')}', '{$this->schemaHelper->getModuleId('news')}');",
            "INSERT INTO `{pre}news` VALUES ('', '{$currentDate}', '{$currentDate}', '{$lang->t('install', 'news_headline')}', '{$lang->t('install', 'news_text')}', '1', '1', '1', '', '', '', '');"
        ];
    }
}