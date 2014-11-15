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
     * @var Core\Assets
     */
    protected $assets;

    /**
     * @param Core\Context\Frontend $frontendContext
     */
    public function __construct(Core\Context\Frontend $frontendContext)
    {
        parent::__construct($frontendContext);

        $this->assets = $frontendContext->getAssets();
    }

    public function actionIndex()
    {
        $this->setNoOutput(true);

        if (!empty($this->request->group)) {
            $libraries = !empty($this->request->libraries) ? explode(',', $this->request->libraries) : [];
            $layout = isset($this->request->layout) && !preg_match('=/=', $this->request->layout) ? $this->request->layout : 'layout';

            $options = [];
            switch ($this->request->group) {
                case 'css':
                    $files = $this->assets->includeCssFiles($libraries, $layout);
                    break;
                case 'js':
                    $files = $this->assets->includeJsFiles($libraries, $layout);
                    break;
                default:
                    $files = [];
            }

            $options['files'] = array_filter($files, function($var) {
                return !empty($var);
            });

            $options['maxAge'] = CONFIG_CACHE_MINIFY;
            $options['minifiers']['text/css'] = array('Minify_CSSmin', 'minify');

            \Minify::setCache(new \Minify_Cache_File(UPLOADS_DIR . 'cache/minify/', true));
            \Minify::serve('Files', $options);
        }
    }

}