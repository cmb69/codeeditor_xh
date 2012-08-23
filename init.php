<?php

/**
 * General editor interface of Codeeditor_XH.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the configuration in JSON format.
 *
 * @param   string $mode  The highlighting mode.
 * @param   string $config  'full', 'medium', 'minimal', 'sidebar' or '' for the
 *                          default init.js, a filename or a JSON object
 * @return  string
 */
function codeeditor_config($mode, $config) {
    global $pth;

    $config = trim($config);
    if (empty($config) || $config[0] !== '{') {
        $std = in_array($config,
                        array('full', 'medium', 'minimal', 'sidebar', ''));
        $fn = $std
            ? $pth['folder']['plugins'] . 'codeeditor/inits/init.js'
            : $fn;
        $config = file_get_contents($fn);
        if ($config === false) {
            $config = '{}';
        }
    }
    $config = str_replace(array(' ', "\t", "\r", "\n"), '', $config);
    $config = str_replace('%MODE%', $mode, $config);
    return $config;
}
//function codeeditor_config() {
//    global $plugin_cf;
//
//    $pcf = $plugin_cf['codeeditor'];
//
//    return 'theme: \''.$pcf['theme'].'\','
//	    .' indentUnit: '.$pcf['indent_unit'].','
//	    .' tabSize: '.$pcf['tab_size'].','
//	    .' indentWithTabs: '.($pcf['indent_with_tabs'] ? 'true' : 'false').','
//	    .' tabMode: \''.$pcf['tab_mode'].'\','
//	    .' electricChars: '.($pcf['electric_chars'] ? 'true' : 'false').','
//	    .' extraKeys: {'
//	    .'     \'Esc\': CODEEDITOR.toggleFullscreen,'
//	    .'     \'Ctrl-Q\': function(cm) {CODEEDITOR.foldFunc(cm, cm.getCursor().line);},'
//	    .'     \'Ctrl-I\': function(cm) {CODEEDITOR.filebrowser(\'images\')},'
//	    .'     \'Ctrl-L\': function(cm) {CODEEDITOR.filebrowser(\'downloads\')},'
//	    .'     \'Alt-W\': function(cm) {cm.setOption(\'lineWrapping\', !cm.getOption(\'lineWrapping\'))}'
//	    .' },'
//	    .' lineWrapping: '.($pcf['line_wrapping'] ? 'true' : 'false').','
//	    .' lineNumbers: '.($pcf['line_numbers'] ? 'true' : 'false').','
//	    .' firstLineNumber: '.$pcf['first_line_number'].','
//	    .' matchBrackets: '.($pcf['match_brackets'] ? 'true' : 'false').','
//	    .' workTime: '.$pcf['work_time'].','
//	    .' workDelay: '.$pcf['work_delay'].','
//	    .' onFocus: CODEEDITOR.onFocus,'
//	    .' undoDepth: '.$pcf['undo_depth'].','
//	    .' onCursorActivity: CODEEDITOR.onCursorActivity,'
//	    .' onGutterClick: CODEEDITOR.foldFunc';
//}


/**
 * Return a tool button.
 *
 * @param string $name
 * @param string $js
 * @return string  The (X)HTML.
 */
//function codeeditor_tool($name, $js) {
//    global $pth;
//
//    $imgdir = $pth['folder']['plugins'].'codeeditor/images/';
//    return '<a href="javascript:'.$js.'">'
//	    .tag('img src="'.$imgdir.$name.'.png" alt="'.$name.'" title="'.$name.'"') // TODO i18n
//	    .'</a>';
//}


/**
 * Returns the prototype of the toolbar.
 */
//function codeeditor_toolbar() {
//    $o = '<div id="codeeditor_toolbar" class="codeeditor_toolbar" style="display:none">'."\n";
//    $tools = array(
//	'save' => 'CODEEDITOR.getForm().submit()',
//	'image' => 'CODEEDITOR.filebrowser(\'images\')',
//	'link' => 'CODEEDITOR.filebrowser(\'downloads\')',
//	'undo' => 'CODEEDITOR.undo()',
//	'redo' => 'CODEEDITOR.redo()',
//	'search' => 'CodeMirror.commands.find(CODEEDITOR.current)',
//	'wrap' => 'CODEEDITOR.instances[0].setOption(\'lineWrapping\', !CODEEDITOR.instances[0].getOption(\'lineWrapping\'))',
//	'fullscreen' => 'CODEEDITOR.toggleFullscreen()'
//    );
//    foreach ($tools as $name => $js) {
//	$o .= codeeditor_tool($name, $js)."\n";
//    }
//    $o .= '</div>'."\n";
//    return $o;
//}


/**
 * Returns the prototype of the statusbar.
 */
//function codeeditor_statusbar() {
//    return '<div id="codeeditor_statusbar" class="codeeditor_statusbar" style="display:none">'."\n"
//	    .'&nbsp;</div>'."\n";
//}


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
    var browser = window.open('$url' + type, 'popWhizz',
	    'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=640,height=480,top=100');
}
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

    $hjs .= '<script type="text/javascript" src="'.$dir.'codemirror/lib/codemirror.js"></script>'."\n"
	    .tag('link rel="stylesheet" type="text/css" href="'.$dir.'codemirror/lib/codemirror.css"')."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/css/css.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/xml/xml.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/javascript/javascript.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/php/php.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/clike/clike.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/mode/htmlmixed/htmlmixed.js"></script>'."\n";

    $hjs .= tag('link rel="stylesheet" type="text/css" href="'.$dir.'codemirror/lib/util/dialog.css"')."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/lib/util/dialog.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/lib/util/searchcursor.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/lib/util/search.js"></script>'."\n"
	    .'<script type="text/javascript" src="'.$dir.'codemirror/lib/util/foldcode.js"></script>'."\n";

    $fn = $dir . 'codemirror/theme/' . $pcf['theme'] . '.css'; // TODO: use theme in config.php?
    if (is_readable($fn)) {
	$hjs .= tag('link rel="stylesheet" type="text/css" href="'.$fn.'"')."\n";
    }
    $hjs .= '<script type="text/javascript" src="' . $dir . 'codeeditor.js"></script>'."\n"
	    .'<script type="text/javascript">'."\n".'/* <![CDATA[ */'."\n"
	    .'codeeditor.text = {'."\n"
	    .'    save: \''.addcslashes(ucfirst($tx['action']['save']), "\0'\"\\\f\n\r\t\v").'\','."\n"
	    .'    confirmLeave: \''.addcslashes($ptx['confirm_leave'], "\0'\"\\\f\n\r\t\v").'\','."\n"
	    .'    noChanges: \''.addcslashes($ptx['no_changes'], "\0'\"\\\f\n\r\t\v").'\''."\n"
	    .'}'."\n"
	    .'codeeditor.xhtml = '.($cf['xhtml']['endtags'] == 'true' ? 'true' : 'false').';'."\n"
	    .'/* ]]> */'."\n".'</script>'."\n";
    $hjs .= '<script type="text/javascript">'.codeeditor_filebrowser().'</script>'."\n";
}


/**
 * Returns the JS to actually instantiate a single editor on the textarea given by $element_id.
 * $config can be 'full', 'medium', 'minimal', 'sidebar' or '' (which will use the users default configuration).
 * Other values are editor dependent. Typically this will be a string in JSON format enclosed in { },
 * that can contain %PLACEHOLDER%s, that will be substituted.
 *
 * To actually create the editor, the caller has to write the the return value to the (X)HTML output,
 * properly enclosed as <script>, after the according <textarea>, or execute the return value by other means.
 *
 * @param string $elementId  The id of the textarea that should become an editor instance.
 * @param string $config  The configuration string.
 * @return string  The JS to actually create the editor.
 */
function codeeditor_replace($elementId, $config = '') {
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
function init_codeeditor($classes = array(), $config = false) {
    global $hjs, $onload;

    include_codeeditor();
    if (empty($classes)) {$classes = array('xh-editor');}
    $classes = implode('|', $classes);
    $config = codeeditor_config('htmlmixed', $config);
    $onload .= "codeeditor.instantiateByClasses('$classes', $config, true);";
}


?>
