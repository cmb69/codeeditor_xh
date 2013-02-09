<?php

/**
 * Back-end of Codeeditor_XH.
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
 * The version number.
 */
define('CODEEDITOR_VERSION', '1beta6');


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function codeeditor_version()
{
    return '<h1>Codeeditor_XH</h1>'
	. '<p>Version: '.CODEEDITOR_VERSION.'</p>'
	. '<p><a href="http://3-magi.net/?CMSimple_XH/Codeeditor_XH">Codeeditor_XH</a> is powered by '
	. '<a href="http://codemirror.net/" target="_blank">'
	. 'CodeMirror</a>.</p>'
	. '<p>Copyright &copy; 2011-2013 <a href="http://3-magi.net">Christoph M. Becker</a></p>'
	. '<p style="text-align:justify">This program is free software: you can redistribute it and/or modify'
	. ' it under the terms of the GNU General Public License as published by'
	. ' the Free Software Foundation, either version 3 of the License, or'
	. ' (at your option) any later version.</p>'
	. '<p style="text-align:justify">This program is distributed in the hope that it will be useful,'
	. ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	. ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	. ' GNU General Public License for more details.</p>'
	. '<p style="text-align:justify">You should have received a copy of the GNU General Public License'
	. ' along with this program.  If not, see'
	. ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>';
}


/**
 * Returns requirements information view.
 *
 * @return string  The (X)HTML.
 */
function codeeditor_system_check() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx, $plugin_cf;

    define('CODEEDITOR_PHP_VERSION', '4.3.0');
    $ptx = $plugin_tx['codeeditor'];
    $imgdir = $pth['folder']['plugins'] . 'codeeditor/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
	. (version_compare(PHP_VERSION, CODEEDITOR_PHP_VERSION) >= 0 ? $ok : $fail)
	. '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], CODEEDITOR_PHP_VERSION)
	. tag('br') . tag('br');
    foreach (array() as $ext) {
	$o .= (extension_loaded($ext) ? $ok : $fail)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext) . tag('br');
    }
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	. '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br');
    $o .= (!get_magic_quotes_runtime() ? $ok : $warn)
	. '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . tag('br');
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'codeeditor/' . $folder;
    }
    foreach ($folders as $folder) {
	$o .= (is_writable($folder) ? $ok : $warn)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder) . tag('br');
    }
    return $o;
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
	$dir = $pth['folder']['plugins'] . 'codeeditor/';
	include_once $pth['folder']['plugins'] . 'codeeditor/init.php';
	include_codeeditor();
	$config = codeeditor_config($mode, '');
	if (CMSIMPLE_XH_BUILD < '2010112201') {
	    $onload .= 'codeeditor.setClass();';
	}
	$onload .= "codeeditor.instantiateByClasses('$class', $config, false);";
    }
}


/*
 * Handle the plugin administration.
 */
if (isset($codeeditor) && $codeeditor == 'true') {
    $o .= print_plugin_admin('off');

    switch ($admin) {
    case '':
	$o .= codeeditor_version() . codeeditor_system_check();
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}


/*
 * Activate CodeMirror.
 */
if ($plugin_cf['codeeditor']['enabled']) {
    codeeditor();
}

?>
