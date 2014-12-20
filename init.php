<?php

/**
 * General editor interface of Codeeditor_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Codeeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Codeeditor_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    header('Content-Type: text/plain; charset=utf-8');
    exit('Access forbidden');
}

/*
 * Provide JSON handling, if not already there.
 */
if (!function_exists('json_encode')) {
    if (!class_exists('CMB_JSON')) {
        /**
         * The JSON fallback.
         */
        include_once $pth['folder']['plugins'] . 'codeditor/JSON.php';
    }
    /**
     * Returns the JSON representation of a value.
     *
     * @param mixed $value A value.
     *
     * @return string
     *
     * @todo Don't inject json_encode().
     */
// @codingStandardsIgnoreStart
    function json_encode($value)
    {
// @codingStandardsIgnoreEnd
        $json = CMB_JSON::instance();
        return $json->encode($value);
    }
    /**
     * Decodes a JSON string.
     *
     * @param string $string A JSON string.
     *
     * @return mixed
     *
     * @todo Don't inject json_decode().
     */
// @codingStandardsIgnoreStart
    function json_decode($string)
    {
// @codingStandardsIgnoreEnd
        $json = CMB_JSON::instance();
        return $json->decode($string);
    }
}

/**
 * Returns the configuration in JSON format.
 *
 * The configuration string can be `full', `medium', `minimal', `sidebar'
 * or `' (which will use the users default configuration).
 * Other values are taken as file name or as JSON configuration object.
 *
 * @param string $mode   The syntax mode.
 * @param string $config The configuration string.
 *
 * @return string
 *
 * @global array  The paths of system files and folders.
 * @global string Error messages as (X)HTML `li' elements.
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 */
function Codeeditor_config($mode, $config)
{
    global $pth, $e, $plugin_cf, $plugin_tx;

    $pcf = $plugin_cf['codeeditor'];
    $ptx = $plugin_tx['codeeditor'];
    $config = trim($config);
    if (empty($config) || $config[0] != '{') {
        $std = in_array(
            $config, array('full', 'medium', 'minimal', 'sidebar', '')
        );
        $fn = $std
            ? $pth['folder']['plugins'] . 'codeeditor/inits/init.json'
            : $config;
        $config = ($config = file_get_contents($fn)) !== false ? $config : '{}';
    }
    $parsedConfig = json_decode($config, true);
    if (!is_array($parsedConfig)) {
        $e .= '<li><b>' . $ptx['error_json'] . '</b>' . tag('br')
            . (isset($fn) ? $fn : htmlspecialchars($config, ENT_QUOTES, 'UTF-8'))
            . '</li>';
        return null;
    }
    $config = $parsedConfig;
    if (!isset($config['mode']) || $config['mode'] == '%MODE%') {
        $config['mode'] = $mode;
    }
    if (!isset($config['theme']) || $config['theme'] == '%THEME%') {
        $config['theme'] = $pcf['theme'];
    }
    $config = json_encode($config);
    return $config;
}

/**
 * Returns the JavaScript to activate the configured filebrowser.
 *
 * @return void
 *
 * @global bool  Whether the user is logged in as admin.
 * @global array The paths of system files and folders.
 * @global array The configuration of the core.
 */
function Codeeditor_filebrowser()
{
    global $adm, $pth, $cf;

    // no filebrowser, if editor is called from front-end
    if (!$adm) {
        return '';
    }

    $script = '';
    if (!empty($cf['filebrowser']['external'])) {
        $connector = $pth['folder']['plugins'] . $cf['filebrowser']['external']
            . '/connectors/codeeditor/codeeditor.php';
        if (is_readable($connector)) {
            include_once $connector;
            $init = $cf['filebrowser']['external'] . '_codeeditor_init';
            if (function_exists($init)) {
                $script = call_user_func($init);
            }
        }
    } else {
        $_SESSION['codeeditor_fb_callback'] = 'wrFilebrowser';
        $url =  $pth['folder']['plugins']
            . 'filebrowser/editorbrowser.php?editor=codeeditor&prefix='
            . CMSIMPLE_BASE . '&base=./&type=';
        $script = <<<EOS
/* <![CDATA[ */
codeeditor.filebrowser = function(type) {
    window.open("$url" + type, "codeeditor_filebrowser",
            "toolbar=no,location=no,status=no,menubar=no," +
            "scrollbars=yes,resizable=yes,width=640,height=480");
}
/* ]]> */
EOS;
    }
    return $script;
}

/**
 * Writes the basic JavaScript of the editor to the `head' element.
 * No editors are actually created. Multiple calls are allowed.
 * This is called from init_EDITOR() automatically, but not from EDITOR_replace().
 *
 * @return void
 *
 * @global string (X)HTML to insert in the `head' element.
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the plugins.
 * @global array  The localization of the plugins.
 */
// @codingStandardsIgnoreStart
function include_codeeditor()
{
// @codingStandardsIgnoreEnd
    global $hjs, $pth, $plugin_cf, $plugin_tx;
    static $again = false;

    if ($again) {
        return;
    }
    $again = true;

    $pcf = $plugin_cf['codeeditor'];
    $ptx = $plugin_tx['codeeditor'];
    $dir = $pth['folder']['plugins'] . 'codeeditor/';

    $css = tag(
        'link rel="stylesheet" type="text/css" href="' . $dir
        . 'codemirror/lib/codemirror.css"'
    );
    $css .= tag(
        'link rel="stylesheet" type="text/css" href="' . $dir
        . 'codemirror/lib/util/dialog.css"'
    );
    $fn = $dir . 'codemirror/theme/' . $pcf['theme'] . '.css';
    $css .= file_exists($fn)
        ? tag('link rel="stylesheet" type="text/css" href="' . $fn . '"')
        : '';
    $text = array('confirm_leave' => $ptx['confirm_leave']);
    $text = json_encode($text);
    $filebrowser = Codeeditor_filebrowser();

    $hjs .= <<<EOS
$css
<script type="text/javascript" src="{$dir}codemirror/lib/codemirror.min.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/modes.min.js"></script>
<script type="text/javascript" src="{$dir}codemirror/lib/utils.min.js"></script>
<script type="text/javascript" src="{$dir}codeeditor.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
codeeditor.text = $text;
/* ]]> */
$filebrowser
</script>
EOS;
}

/**
 * Returns the JavaScript to actually instantiate a single editor a
 * `textarea' element.
 *
 * To actually create the editor, the caller has to write the the return value
 * to the (X)HTML output, properly enclosed as `script' element,
 * after the according `textarea' element,
 * or execute the return value by other means.
 *
 * @param string $elementId The id of the `textarea' element that should become
 *                          an editor instance.
 * @param string $config    The configuration string.
 *
 * @return string The JavaScript to actually create the editor.
 */
function Codeeditor_replace($elementId, $config = '')
{
    $config = Codeeditor_config('htmlmixed', $config);
    return "codeeditor.instantiate('$elementId', $config, true);";
}

/**
 * Instantiates the editor(s) on the textarea(s) given by $classes.
 * $config is exactly the same as for EDITOR_replace().
 *
 * @param string $classes The classes of the textarea(s) that should become
 *                        an editor instance.
 * @param string $config  The configuration string.
 *
 * @return void
 *
 * global string (X)HTML to insert in the `head' element.
 * global string (X)HTML to insert at the bottom of the `body' element.
 */
// @codingStandardsIgnoreStart
function init_codeeditor($classes = array(), $config = false)
{
// @codingStandardsIgnoreEnd
    global $hjs, $bjs;

    include_codeeditor();
    if (empty($classes)) {
        $classes = array('xh-editor');
    }
    $classes = json_encode($classes);
    $config = Codeeditor_config('htmlmixed', $config);
    $script = <<<EOS
<script type="text/javascript">
/* <![CDATA[ */
codeeditor.addEventListener(window, "load", function() {
    codeeditor.instantiateByClasses($classes, $config, true);
})
/* ]]> */
</script>

EOS;
    if (isset($bjs)) {
        $bjs .= $script;
    } else {
        $hjs .= $script;
    }
}

/*
 * Include config and language file, if not yet done.
 */
global $sl; // file can be included from within a function
if (!isset($cf['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/config/config.php';
}
if (!isset($tx['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/languages/' . $sl . '.php';
}

?>
