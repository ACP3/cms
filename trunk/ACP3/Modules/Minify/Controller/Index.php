<?php

namespace ACP3\Modules\Minify\Controller;

use ACP3\Core;
use ACP3\Modules\Minify;

/**
 * Class Index
 * @package ACP3\Modules\Minify\Controller
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Minify\Helpers
     */
    protected $minifyHelpers;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Minify\Helpers $minifyHelpers)
    {
       parent::__construct($context, $breadcrumb, $seo);

        $this->minifyHelpers = $minifyHelpers;
    }

    public function actionIndex()
    {
        $this->setNoOutput(true);

        if (!empty($this->uri->group)) {
            $libraries = !empty($this->uri->libraries) ? explode(',', $this->uri->libraries) : array();
            $layout = isset($this->uri->layout) && !preg_match('=/=', $this->uri->layout) ? $this->uri->layout : 'layout';

            $options = array();
            switch ($this->uri->group) {
                case 'css':
                    $files = $this->minifyHelpers->includeCssFiles($libraries, $layout);
                    break;
                case 'js':
                    $files = $this->minifyHelpers->includeJsFiles($libraries, $layout);
                    break;
                default:
                    $files = array();
            }
            $options['files'] = $files;
            $options['maxAge'] = CONFIG_CACHE_MINIFY;
            $options['minifiers']['text/css'] = array('Minify_CSSmin', 'minify');

            \Minify::setCache(new \Minify_Cache_File(UPLOADS_DIR . 'cache/minify/', true));
            \Minify::serve('Files', $options);
        }
    }

}
