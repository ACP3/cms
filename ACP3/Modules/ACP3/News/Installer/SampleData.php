<?php
namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\Date;
use ACP3\Core\I18n\Translator;
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
        $currentDate = gmdate(Date::DEFAULT_DATE_FORMAT_FULL);
        /** @var Translator $translator */
        $translator = $this->schemaHelper->getContainer()->get('core.lang');

        return [
            "INSERT INTO `{pre}categories` VALUES ('', '{$translator->t('install', 'category_name')}', '', '{$translator->t('install', 'category_description')}', '{$this->schemaHelper->getModuleId(Schema::MODULE_NAME)}');",
            "INSERT INTO `{pre}news` VALUES ('', 1, '{$currentDate}', '{$currentDate}', '{$currentDate}', '{$translator->t('install', 'news_headline')}', '{$translator->t('install', 'news_text')}', '1', '1', '1', '', '', '', '1');"
        ];
    }
}
