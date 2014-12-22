<?php

namespace ACP3\Core\WYSIWYG\CKEditor;

/**
 * Class Initialize
 * @package ACP3\Core\WYSIWYG\CKEditor
 */
class Initialize
{
    /**
     * The version of %CKEditor.
     */
    const VERSION = '4.4.6';

    /**
     * URL to the %CKEditor installation directory (absolute or relative to document root).
     * If not set, CKEditor will try to guess it's path.
     *
     * @var string
     */
    public $basePath = '';
    /**
     * An array that holds the global %CKEditor configuration.
     * For the list of available options, see http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
     *
     * @var array
     */
    public $config = [];
    /**
     * @var bool
     */
    public $initialized = false;
    /**
     * Boolean variable indicating whether created code should be printed out or returned by a function.
     *
     * @var bool
     */
    public $returnOutput = false;
    /**
     * An array with textarea attributes.
     *
     * @var array
     */
    public $textareaAttributes = ["rows" => 8, "cols" => 60];
    /**
     * An array that holds event listeners.
     *
     * @var array
     */
    private $events = [];
    /**
     * An array that holds global event listeners.
     *
     * @var array
     */
    private $globalEvents = [];
    /**
     * @var array
     */
    private $returnedEvents = [];

    /**
     * Main Constructor.
     *
     * @param null $basePath
     */
    function __construct($basePath = null)
    {
        if (!empty($basePath)) {
            $this->basePath = $basePath;
        }
    }

    /**
     * @param        $name
     * @param        $id
     * @param string $value
     * @param array  $config
     * @param array  $events
     *
     * @return string
     */
    public function editor($name, $id, $value = "", $config = [], $events = [])
    {
        $attr = "";
        foreach ($this->textareaAttributes as $key => $val) {
            $attr .= " " . $key . '="' . str_replace('"', '&quot;', $val) . '"';
        }
        $out = "<textarea name=\"" . $name . "\" id=\"" . $id . "\"" . $attr . ">" . htmlspecialchars($value) . "</textarea>\n";
        if (!$this->initialized) {
            $out .= $this->init();
        }

        $_config = $this->configSettings($config, $events);

        $js = $this->_returnGlobalEvents();
        if (!empty($_config)) {
            $js .= "CKEDITOR.replace('" . $name . "', " . $this->jsEncode($_config) . ");";
        } else {
            $js .= "CKEDITOR.replace('" . $name . "');";
        }

        $out .= $this->script($js);

        if (!$this->returnOutput) {
            echo $out;
            $out = "";
        }

        return $out;
    }

    /**
     * @param       $id
     * @param array $config
     * @param array $events
     *
     * @return string
     */
    public function replace($id, $config = [], $events = [])
    {
        $out = "";
        if (!$this->initialized) {
            $out .= $this->init();
        }

        $_config = $this->configSettings($config, $events);

        $js = $this->_returnGlobalEvents();
        if (!empty($_config)) {
            $js .= "CKEDITOR.replace('" . $id . "', " . $this->jsEncode($_config) . ");";
        } else {
            $js .= "CKEDITOR.replace('" . $id . "');";
        }
        $out .= $this->script($js);

        if (!$this->returnOutput) {
            echo $out;
            $out = "";
        }

        return $out;
    }

    /**
     * @param null $className
     *
     * @return string
     */
    public function replaceAll($className = null)
    {
        $out = "";
        if (!$this->initialized) {
            $out .= $this->init();
        }

        $_config = $this->configSettings();

        $js = $this->_returnGlobalEvents();
        if (empty($_config)) {
            if (empty($className)) {
                $js .= "CKEDITOR.replaceAll();";
            } else {
                $js .= "CKEDITOR.replaceAll('" . $className . "');";
            }
        } else {
            $js .= "CKEDITOR.replaceAll( function(textarea, config) {\n";
            if (!empty($className)) {
                $js .= "	var classRegex = new RegExp('(?:^| )' + '" . $className . "' + '(?:$| )');\n";
                $js .= "	if (!classRegex.test(textarea.className))\n";
                $js .= "		return false;\n";
            }
            $js .= "	CKEDITOR.tools.extend(config, " . $this->jsEncode($_config) . ", true);";
            $js .= "} );";

        }

        $out .= $this->script($js);

        if (!$this->returnOutput) {
            print $out;
            $out = "";
        }

        return $out;
    }

    /**
     * @param $event
     * @param $javascriptCode
     */
    public function addEventHandler($event, $javascriptCode)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        // Avoid duplicates.
        if (!in_array($javascriptCode, $this->events[$event])) {
            $this->events[$event][] = $javascriptCode;
        }
    }

    /**
     * @param null $event
     */
    public function clearEventHandlers($event = null)
    {
        if (!empty($event)) {
            $this->events[$event] = [];
        } else {
            $this->events = [];
        }
    }

    /**
     * @param $event
     * @param $javascriptCode
     */
    public function addGlobalEventHandler($event, $javascriptCode)
    {
        if (!isset($this->globalEvents[$event])) {
            $this->globalEvents[$event] = [];
        }

        // Avoid duplicates.
        if (!in_array($javascriptCode, $this->globalEvents[$event])) {
            $this->globalEvents[$event][] = $javascriptCode;
        }
    }

    /**
     * @param null $event
     */
    public function clearGlobalEventHandlers($event = null)
    {
        if (!empty($event)) {
            $this->globalEvents[$event] = [];
        } else {
            $this->globalEvents = [];
        }
    }

    /**
     * Prints javascript code.
     *
     * @param $js
     *
     * @return string
     */
    private function script($js)
    {
        $out = "<script type=\"text/javascript\">";
        $out .= "//<![CDATA[\n";
        $out .= $js;
        $out .= "\n//]]>";
        $out .= "</script>\n";

        return $out;
    }

    /**
     * @param array $config
     * @param array $events
     *
     * @return array
     */
    private function configSettings($config = [], $events = [])
    {
        $_config = $this->config;
        $_events = $this->events;

        if (is_array($config) && !empty($config)) {
            $_config = array_merge($_config, $config);
        }

        if (is_array($events) && !empty($events)) {
            foreach ($events as $eventName => $code) {
                if (!isset($_events[$eventName])) {
                    $_events[$eventName] = [];
                }
                if (!in_array($code, $_events[$eventName])) {
                    $_events[$eventName][] = $code;
                }
            }
        }

        if (!empty($_events)) {
            foreach ($_events as $eventName => $handlers) {
                if (empty($handlers)) {
                    continue;
                } else if (count($handlers) == 1) {
                    $_config['on'][$eventName] = '@@' . $handlers[0];
                } else {
                    $_config['on'][$eventName] = '@@function (ev){';
                    foreach ($handlers as $handler => $code) {
                        $_config['on'][$eventName] .= '(' . $code . ')(ev);';
                    }
                    $_config['on'][$eventName] .= '}';
                }
            }
        }

        return $_config;
    }

    /**
     * @return string
     */
    private function _returnGlobalEvents()
    {
        $out = "";

        if (!empty($this->globalEvents)) {
            foreach ($this->globalEvents as $eventName => $handlers) {
                foreach ($handlers as $handler => $code) {
                    if (!isset($this->returnedEvents[$eventName])) {
                        $this->returnedEvents[$eventName] = [];
                    }

                    // Return only new events
                    if (!in_array($code, $this->returnedEvents[$eventName])) {
                        $out .= ($code ? "\n" : "") . "CKEDITOR.on('" . $eventName . "', $code);";
                        $this->returnedEvents[$eventName][] = $code;
                    }
                }
            }
        }

        return $out;
    }

    /**
     * @return string
     */
    private function init()
    {
        if ($this->initialized === true) {
            return "";
        }

        $this->initialized = true;
        $out = "";
        $ckeditorPath = $this->ckeditorPath();

        // Skip relative paths...
        if (strpos($ckeditorPath, '..') !== 0) {
            $out .= $this->script("window.CKEDITOR_BASEPATH='" . $ckeditorPath . "';");
        }

        $out .= "<script type=\"text/javascript\" src=\"" . $ckeditorPath . 'ckeditor.js?v=' . self::VERSION . "\"></script>\n";

        return $out;
    }

    /**
     * @return mixed|null|string
     */
    private function ckeditorPath()
    {
        if (!empty($this->basePath)) {
            return $this->basePath;
        }

        /**
         * The absolute pathname of the currently executing script.
         * Note: If a script is executed with the CLI, as a relative path, such as file.php or ../file.php,
         * $_SERVER['SCRIPT_FILENAME'] will contain the relative path specified by the user.
         */
        $realPath = isset($_SERVER['SCRIPT_FILENAME']) ? dirname($_SERVER['SCRIPT_FILENAME']) : realpath('./');

        /**
         * The filename of the currently executing script, relative to the document root.
         * For instance, $_SERVER['PHP_SELF'] in a script at the address http://example.com/test.php/foo.bar
         * would be /test.php/foo.bar.
         */
        $selfPath = dirname($_SERVER['PHP_SELF']);
        $file = str_replace("\\", "/", __FILE__);

        if (!$selfPath || !$realPath || !$file) {
            return "/ckeditor/";
        }

        $documentRoot = substr($realPath, 0, strlen($realPath) - strlen($selfPath));
        $fileUrl = substr($file, strlen($documentRoot));
        $ckeditorUrl = str_replace("ckeditor_php5.php", "", $fileUrl);

        return $ckeditorUrl;
    }

    /**
     * @param $val
     *
     * @return mixed|string
     */
    private function jsEncode($val)
    {
        if (is_null($val)) {
            return 'null';
        }
        if (is_bool($val)) {
            return $val ? 'true' : 'false';
        }
        if (is_int($val)) {
            return $val;
        }
        if (is_float($val)) {
            return str_replace(',', '.', $val);
        }
        if (is_array($val) || is_object($val)) {
            if (is_array($val) && (array_keys($val) === range(0, count($val) - 1))) {
                return '[' . implode(',', array_map([$this, 'jsEncode'], $val)) . ']';
            }
            $temp = [];
            foreach ($val as $k => $v) {
                $temp[] = $this->jsEncode("{$k}") . ':' . $this->jsEncode($v);
            }
            return '{' . implode(',', $temp) . '}';
        }

        // String otherwise
        if (strpos($val, '@@') === 0) {
            return substr($val, 2);
        }
        if (strtoupper(substr($val, 0, 9)) == 'CKEDITOR.') {
            return $val;
        }

        return '"' . str_replace(["\\", "/", "\n", "\t", "\r", "\x08", "\x0c", '"'], ['\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'], $val) . '"';
    }
}
