<?php

/**
 * Administration of Codeeditor_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Codeeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Codeeditor_XH
 */

/*
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Codeeditor_XH detected an unsupported CMSimple_XH version.
Uninstall Codeeditor_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

/**
 * The version number.
 */
define('CODEEDITOR_VERSION', '@CODEEDITOR_VERSION@');

/**
 * Returns the plugin version information view.
 *
 * @return string The (X)HTML.
 *
 * @global array The paths of system files and folders.
 */
function Codeeditor_version()
{
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Codeeditor_XH">'
        . 'Codeeditor_XH</a></h1>'
        . tag(
            'img src="' . $pth['folder']['plugins'] . 'codeeditor/codeeditor.png"'
            . 'alt="Plugin Icon" style="float: left"'
        )
        . '<p>Version: ' . CODEEDITOR_VERSION . '</p>'
        . '<p>Codeeditor_XH is powered by '
        . '<a href="http://codemirror.net/" target="_blank">'
        . 'CodeMirror</a>.</p>'
        . '<p>Copyright &copy; 2011-2014 <a href="http://3-magi.net">'
        . 'Christoph M. Becker</a></p>'
        . '<p style="text-align:justify">This program is free software:'
        . 'you can redistribute it and/or modify'
        . ' it under the terms of the GNU General Public License as published by'
        . ' the Free Software Foundation, either version 3 of the License, or'
        . ' (at your option) any later version.</p>'
        . '<p style="text-align:justify">This program is distributed in the hope'
        . 'that it will be useful,'
        . ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
        . ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
        . ' GNU General Public License for more details.</p>'
        . '<p style="text-align:justify">You should have received a copy of the'
        . 'GNU General Public License'
        . ' along with this program.  If not, see'
        . ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>'
        . '.</p>';
}

/**
 * Returns requirements information view.
 *
 * @return string The (X)HTML.
 *
 * @global array The paths of systems files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Codeeditor_systemCheck()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.0';
    $ptx = $plugin_tx['codeeditor'];
    $imgdir = $pth['folder']['plugins'] . 'codeeditor/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . tag('br');
    foreach (array('pcre') as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . tag('br');
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes']
        . tag('br') . tag('br');
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br');
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'].'codeeditor/' . $folder;
    }
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br');
    }
    return $o;
}

/**
 * Initializes CodeMirror for template and (plugin) stylesheets.
 *
 * @return void
 *
 * @global array  The paths of system files and folders.
 * @global string (X)HTML to be inserted in the `head' element.
 * @global string (X)HTML to be inserted at the bottom of the `body' element.
 * @global string The value of the `onload' attribute of the `body' element.
 * @global string The value of the `admin' parameter.
 * @global string The value of the `action' parameter.
 * @global string The value of the `file' parameter.
 */
function codeeditor()
{
    global $pth, $hjs, $bjs, $onload, $admin, $action, $file;

    // TODO: is this necessary? (it doesn't hurt though)
    initvar('admin');
    initvar('action');
    initvar('file');

    if ($file == 'template' && ($action == 'edit' || $action == '')
        || $file == 'content' && ($action == 'edit' || $action == '')
    ) {
        $mode = 'php';
        $class = 'xh_file_edit';
    } elseif ($file == 'stylesheet' && ($action == 'edit' || $action == '')
        || $admin == 'plugin_stylesheet' && $action == 'plugin_text'
    ) {
        $mode = 'css';
        $class = 'xh_file_edit';
    } else {
        $mode = false;
    }

    if ($mode) {
        $dir = $pth['folder']['plugins'] . 'codeeditor/';
        include_once $pth['folder']['plugins'] . 'codeeditor/init.php';
        include_codeeditor();
        $config = Codeeditor_config($mode, '');
        $classes = json_encode(array($class));
        $script = <<<EOS
<script type="text/javascript">
/* <![CDATA[ */
codeeditor.addEventListener(window, "load", function() {
    codeeditor.instantiateByClasses($classes, $config);
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
}

/*
 * Handle the plugin administration.
 */
if (isset($codeeditor) && $codeeditor == 'true') {
    $o .= print_plugin_admin('off');

    switch ($admin) {
    case '':
        $o .= Codeeditor_version() . tag('hr') . Codeeditor_systemCheck();
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
