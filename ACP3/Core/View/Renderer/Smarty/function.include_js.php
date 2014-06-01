<?php
/**
 * Fügt die angegebene JavaScript-Datei in ein Template ein
 *
 * @param array $params
 * @return string
 */
function smarty_function_include_js($params)
{
    static $alreadyIncluded = array();

    if (isset($params['module'], $params['file']) === true &&
        (bool)preg_match('=/=', $params['module']) === false &&
        (bool)preg_match('=/=', $params['file']) === false
    ) {
        // Do not include the same file multiple times
        $key = $params['module'] . '/' . $params['file'];
        if (isset($alreadyIncluded[$key]) === false) {
            if (!empty($params['depends'])) {
                \ACP3\Core\Registry::get('View')->enableJsLibraries(explode(',', $params['depends']));
            }

            $alreadyIncluded[$key] = true;

            $script = '<script type="text/javascript" src="%s"></script>';
            $module = ucfirst($params['module']);
            $file = $params['file'];

            if (is_file(DESIGN_PATH_INTERNAL . $module . '/js/' . $file . '.js') === true) {
                return sprintf($script, DESIGN_PATH . $module . '/js/' . $file . '.js');
            } elseif (is_file(MODULES_DIR . $module . '/js/' . $file . '.js') === true) {
                return sprintf($script, ROOT_DIR . 'ACP3/Modules/' . $module . '/js/' . $file . '.js');
            }
        }
        return '';
    }

    return 'Not all necessary arguments for the function ' . __FUNCTION__ . ' were passed!';
}
/* vim: set expandtab: */