<?php

/**
 * Back-end of Codeeditor_XH.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('CODEEDITOR_VERSION', '1beta1');


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function codeeditor_version() {
    return '<h1>Codeeditor_XH</h1>'."\n"
	    .'<p>Version: '.CODEEDITOR_VERSION.'</p>'."\n"
	    .'<p><a href="http://3-magi.net/?CMSimple_XH/Codeeditor_XH">Codeeditor_XH</a> is powered by '
	    .'<a href="http://codemirror.net/" target="_blank">'
	    .'CodeMirror</a>.</p>'."\n"
	    .'<p>Copyright &copy; 2011-2012 <a href="http://3-magi.net">Christoph M. Becker</a></p>'."\n"
	    .'<p style="text-align:justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align:justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align:justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information view.
 *
 * @return string  The (X)HTML.
 */
function codeeditor_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx, $plugin_cf;

    define('CODEEDITOR_PHP_VERSION', '4.0.7');
    $ptx =& $plugin_tx['codeeditor'];
    $imgdir = $pth['folder']['plugins'].'codeeditor/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = tag('hr').'<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, CODEEDITOR_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], CODEEDITOR_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array() as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br')."\n";
    $htm .= (!get_magic_quotes_runtime() ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'codeeditor/'.$folder;
    }
    foreach ($folders as $folder) {
	$htm .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $htm;
}


/**
 * Initializes CodeMirror for template and (plugin) stylesheets.
 *
 * @global string $onload
 * @return void
 */
function codeeditor() {
    global $pth, $onload, $admin, $action, $file;

    // TODO: is this necessary? (it doesn't hurt though)
    initvar('admin');
    initvar('action');
    initvar('file');

    if ($file == 'template' && ($action == 'edit' || $action == 'save')) {
	$mode = 'php';
	$class = 'cmsimplecore_file_edit';
    } elseif ($file == 'stylesheet' && ($action == 'edit' || $action == 'save')) {
	$mode = 'css';
	$class = 'cmsimplecore_file_edit';
    } elseif ($admin == 'plugin_stylesheet' && $action == 'plugin_text') {
	$mode = 'css';
	$class = 'plugintextarea';
    } else {
	$mode = FALSE;
    }

    if ($mode) {
	$dir = $pth['folder']['plugins'].'codeeditor/';
	include_once $pth['folder']['plugins'].'codeeditor/init.php';
	include_codeeditor();
	$config = codeeditor_config($mode);
	$onload .= 'CODEEDITOR.instantiateByClasses(\''.$class.'\', '.$config.', false);';
    }
}


/**
 * Plugin administration.
 */
if (!empty($codeeditor)) {
    initvar('admin');
    initvar('action');

    $o .= print_plugin_admin('off');

    switch ($admin) {
	case '':
	    $o .= codeeditor_version().codeeditor_system_check();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}


/**
 * Activates CodeMirror.
 */
if ($plugin_cf['codeeditor']['enabled']) {
    codeeditor();
}

?>
