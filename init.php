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
 * Returns the configuration in JSON.
 *
 * @return string
 */
function codeeditor_config($mode) {
    global $pth;

    $config = file_get_contents($pth['folder']['plugins'].'codeeditor/inits/init.js');
    $search = array("\r\n", "\r", "\n", '%MODE%');
    $replace = array(' ', ' ', ' ', $mode);
    $config = str_replace($search, $replace, $config);
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


function codeeditor_filebrowser() {
    global $cf, $pth, $sl;

    $script = '';
    if ($cf['filebrowser']['external']) {
	$connector = $pth['folder']['plugins'].$cf['filebrowser']['external'].'/connectors/codeeditor/codeeditor.php';
	if (is_readable($connector)) {
	    include_once $connector;
	    $init = $cf['filebrowser']['external'].'_codeeditor_init';
	    if (function_exists($init)) {
		$script = $init();
	    }
	}
    } else {
	$_SESSION['codeeditor_fb_callback'] = 'wrFilebrowser';
	$prefix = $sl == $cf['language']['default'] ? './' : '../';
	$url =  $pth['folder']['plugins'].'filebrowser/editorbrowser.php?editor=codeeditor&prefix='.$prefix.'&type=';
	//$script = file_get_contents(dirname(__FILE__).'/filebrowser.js');
	//$script = str_replace('%URL%', $url, $script);
	$script = <<<SCRIPT
CODEEDITOR.filebrowser = function(type) {
    var browser = window.open('$url' + type, 'popWhizz',
	    'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=640,height=480,top=100');
    browser.setLink = function(url) {
	switch (type) {
	    case 'images': CODEEDITOR.insertImage(url); break;
	    case 'downloads': CODEEDITOR.insertLink(url); break;
	}
	browser.close();
    }
}

SCRIPT;
    }
    return $script;

}


/**
 * Includes the editor's javascripts to the <head>.
 *
 * @global string $hjs
 * @return void
 */
function include_codeeditor() {
    global $hjs, $o, $pth, $cf, $tx, $plugin_cf, $plugin_tx;
    static $again = FALSE;

    if ($again) {return;}

    $pcf = $plugin_cf['codeeditor'];
    $ptx = $plugin_tx['codeeditor'];
    $dir = $pth['folder']['plugins'].'codeeditor/';

    // TODO: add toolbar in this function??
    //$o .= codeeditor_toolbar().codeeditor_statusbar();

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

    $fn = $dir.'codemirror/theme/'.$pcf['theme'].'.css';
    if (is_readable($fn)) {
	$hjs .= tag('link rel="stylesheet" type="text/css" href="'.$fn.'"')."\n";
    }
    $hjs .= '<script type="text/javascript" src="'.$dir.'codeeditor.js"></script>'."\n"
	    .'<script type="text/javascript">'."\n".'/* <![CDATA[ */'."\n"
	    .'CODEEDITOR.text = {'."\n"
	    .'    save: \''.addcslashes(ucfirst($tx['action']['save']), "\0'\"\\\f\n\r\t\v").'\','."\n"
	    .'    confirmLeave: \''.addcslashes($ptx['confirm_leave'], "\0'\"\\\f\n\r\t\v").'\','."\n"
	    .'    noChanges: \''.addcslashes($ptx['no_changes'], "\0'\"\\\f\n\r\t\v").'\''."\n"
	    .'}'."\n"
	    .'CODEEDITOR.xhtml = '.($cf['xhtml']['endtags'] == 'true' ? 'true' : 'false').';'."\n"
	    .'/* ]]> */'."\n".'</script>'."\n";
}


/**
 * Returns JS to replace a textarea with an editor instance.
 *
 * @param string $id  The textarea's ID.
 * @param string $config  The editor's configuration in JSON.
 * @return string
 */
function codeeditor_replace($id, $config = '') {
    return 'CODEEDITOR.instantiate(\''.$id.'\', '.codeeditor_config('htmlmixed').', true);';
}


/**
 * Replaces textareas with editor instances.
 *
 * @param array $classes  The classes of the textareas that should be replaced.
 * @param mixed $init  Ignored.
 * @global string $onload
 * @return void
 */
function init_codeeditor($classes = array(), $init = FALSE) {
    global $hjs, $onload;

    include_codeeditor();
    $hjs .= '<script type="text/javascript">'.codeeditor_filebrowser().'</script>'."\n";
    if (empty($classes)) {$classes = array('xh-editor');}
    $classes = implode('|', $classes);
    //$config = '{mode: \'htmlmixed\', '.codeeditor_config().'}';
    $config = codeeditor_config('htmlmixed');
    $onload .= 'CODEEDITOR.instantiateByClasses(\''.$classes.'\', '.$config.', true);';
}


?>
