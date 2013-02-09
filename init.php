<?php

/**
 * General editor interface of Codeeditor_XH.
 *
 * @package	Codeeditor
 * @copyright	Copyright (c) 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Codeeditor_XH
 */


/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    header('Content-Type: text/plain; charset=utf-8');
    exit('Access forbidden');
}


/**
 * Returns the configuration in JSON format.
 *
 * @param   string $mode  The highlighting mode.
 * @param   string $config  'full', 'medium', 'minimal', 'sidebar' or '' for the
 *                          default init.json, a filename or a JSON object
 * @return  string
 */
function codeeditor_config($mode, $config)
{
    global $pth, $plugin_cf;

    $pcf = $plugin_cf['codeeditor'];
    $config = trim($config);
    if (empty($config) || $config[0] !== '{') {
        $std = in_array($config,
                        array('full', 'medium', 'minimal', 'sidebar', ''));
        $fn = $std
            ? $pth['folder']['plugins'] . 'codeeditor/inits/init.json'
            : $config;
        $config = ($config = file_get_contents($fn)) !== false ? $config : '{}';
    }
    $config = json_decode($config, true);
    $config['mode'] = $mode;
    if (!isset($config['theme'])) {
	$config['theme'] = $pcf['theme'];
    }
    $config = json_encode($config);
    return $config;
}


/**
 * Returns the JS to activate the configured filebrowser.
 *
 * @return void
 */
function codeeditor_filebrowser()
{
    global $cf, $pth, $sl, $adm;

    // no filebrowser, if editor is called from front-end
    if (!$adm) {
        return '';
    }

    $script = '';
    if (isset($cf['filebrowser']['external'])) {
	if ($cf['filebrowser']['external']) {
	    $connector = $pth['folder']['plugins'] . $cf['filebrowser']['external']
		. '/connectors/codeeditor/codeeditor.php';
	    if (is_readable($connector)) {
		include_once $connector;
		$init = $cf['filebrowser']['external'] . '_codeeditor_init';
		if (function_exists($init)) {
		    $script = $init();
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
    window.open('$url' + type, 'popWhizz',
        'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=640,height=480,top=100');
}
/* ]]> */
EOS;
        }
    } else {
	$script = <<<EOS
/* <![CDATA[ */
codeeditor.filebrowser = function() {}
/* ]]> */
EOS;
    }
    return $script;
}


/**
 * Writes the basic JS of the editor to $hjs. No editors are actually created.
 * Multiple calls are allowed; all but the first should be ignored.
 * This is called from init_EDITOR() automatically, but not from EDITOR_replace().
 *
 * @global string $hjs
 * @return void
 */
function include_codeeditor()
{
    global $hjs, $o, $pth, $cf, $tx, $plugin_cf, $plugin_tx;
    static $again = false;

    if ($again) {
        return;
    }
    $again = true;

    $pcf = $plugin_cf['codeeditor'];
    $ptx = $plugin_tx['codeeditor'];
    $dir = $pth['folder']['plugins'] . 'codeeditor/';

    $css1 = tag('link rel="stylesheet" type="text/css" href="' . $dir
               . 'codemirror/lib/codemirror.css"');
    $css2 = tag('link rel="stylesheet" type="text/css" href="' . $dir
                . 'codemirror/lib/util/dialog.css"');
    $fn = $dir . 'codemirror/theme/' . $pcf['theme'] . '.css';
    $css3 = file_exists($fn)
        ? tag('link rel="stylesheet" type="text/css" href="' . $fn . '"')
        : '';
    $text['confirm_leave'] = addcslashes($ptx['confirm_leave'], "\0..\37\'\\");
    $filebrowser = codeeditor_filebrowser();

    $hjs .= <<<EOS
<script type="text/javascript" src="{$dir}codemirror/lib/codemirror.js"></script>
$css1
<script type="text/javascript" src="{$dir}codemirror/mode/css/css.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/xml/xml.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/php/php.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/clike/clike.js"></script>
<script type="text/javascript" src="{$dir}codemirror/mode/htmlmixed/htmlmixed.js"></script>
$css2
<script type="text/javascript" src="{$dir}codemirror/lib/util/dialog.js"></script>
<script type="text/javascript" src="{$dir}codemirror/lib/util/searchcursor.js"></script>
<script type="text/javascript" src="{$dir}codemirror/lib/util/search.js"></script>
<script type="text/javascript" src="{$dir}codemirror/lib/util/foldcode.js"></script>
$css3
<script type="text/javascript" src="{$dir}codeeditor.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
codeeditor.text = {
    confirmLeave: '$text[confirm_leave]'
}
/* ]]> */
</script>
<script type="text/javascript">
$filebrowser
</script>

EOS;
}


/**
 * Returns the JS to actually instantiate a single editor on the textarea given by $element_id.
 * $config can be 'full', 'medium', 'minimal', 'sidebar' or '' (which will use the users default configuration).
 * Other values are taken as file name or as JSON configuration object enclosed in { },
 * that can contain %PLACEHOLDER%s, that will be substituted.
 *
 * To actually create the editor, the caller has to write the the return value to the (X)HTML output,
 * properly enclosed as <script>, after the according <textarea>, or execute the return value by other means.
 *
 * @param   string $elementId  The id of the textarea that should become an editor instance.
 * @param   string $config  The configuration string.
 * @return  string  The JS to actually create the editor.
 */
function codeeditor_replace($elementId, $config = '')
{
    $config = codeeditor_config('htmlmixed', $config);
    return "codeeditor.instantiate('$elementId', $config, true);";
}


/**
 * Instantiates the editor(s) on the textarea(s) given by $element_classes.
 * $config is exactly the same as for EDITOR_replace().
 *
 * @param string $element_classes  The classes of the textarea(s) that should become an editor instance. An empty array means .xh-editor.
 * @param string $config  The configuration string.
 * @global string $onload
 * @return void
 */
function init_codeeditor($classes = array(), $config = false)
{
    global $hjs, $onload;

    include_codeeditor();
    if (empty($classes)) {
        $classes = array('xh-editor');
    }
    $classes = json_encode($classes);
    $classes = htmlspecialchars($classes, ENT_QUOTES, 'UTF-8');
    $config = codeeditor_config('htmlmixed', $config);
    $config = htmlspecialchars($config, ENT_QUOTES, 'UTF-8');
    $onload .= "codeeditor.instantiateByClasses($classes, $config);";
}


/*
 * Include config and language file, if not yet done.
 */
global $sl; // TODO: is that necessary?
if (!isset($cf['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/config/config.php';
}
if (!isset($tx['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/languages/' . $sl . '.php';
}

?>
