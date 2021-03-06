<?php

/**
 * Copyright 2011-2021 Christoph M. Becker
 *
 * This file is part of Codeeditor_XH.
 *
 * Codeeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Codeeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codeeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Codeeditor;

/**
 * The plugin controller.
 *
 * @category CMSimple_XH
 * @package  Codeeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Codeeditor_XH
 */
class Controller
{
    /**
     * Dispatches on plugin related requests.
     *
     * @return void
     *
     * @global array The configuration of the plugins.
     */
    public static function dispatch()
    {
        global $plugin_cf;

        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(false);
            if ($plugin_cf['codeeditor']['enabled']) {
                self::main();
            }
            if (self::isAdministrationRequested()) {
                self::handleAdministration();
            }
        }
    }

    /**
     * Initializes CodeMirror for template and (plugin) stylesheets.
     *
     * @return void
     */
    public static function main()
    {
        if (self::isEditingPhp()) {
            $mode = 'php';
            $class = 'xh_file_edit';
        } elseif (self::isEditingCss()) {
            $mode = 'css';
            $class = 'xh_file_edit';
        } else {
            return;
        }
        self::init([$class], '', $mode);
    }

    /**
     * @return bool
     */
    private static function isEditingPhp()
    {
        global $action, $file;

        return $file == 'template' && ($action == 'edit' || $action == '')
            || $file == 'content' && ($action == 'edit' || $action == '');
    }

    /**
     * @return bool
     */
    private static function isEditingCss()
    {
        global $admin, $action, $file;

        return $file == 'stylesheet' && ($action == 'edit' || $action == '')
            || $admin == 'plugin_stylesheet' && $action == 'plugin_text';
    }

    /**
     * Returns whether the plugin administration has been requested.
     *
     * @return bool
     *
     * @global string Whether the plugin administration has been requested.
     */
    protected static function isAdministrationRequested()
    {
        return XH_wantsPluginAdministration('codeeditor');
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> GP parameter.
     * @global string The value of the <var>action</var> GP parameter.
     * @global string The (X)HTML fragment to insert into the contents area.
     */
    protected static function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoCommand)();
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'codeeditor');
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
    protected static function config($mode, $config)
    {
        global $pth, $e, $plugin_cf, $plugin_tx;

        $pcf = $plugin_cf['codeeditor'];
        $ptx = $plugin_tx['codeeditor'];
        $config = trim($config);
        if (empty($config) || $config[0] != '{') {
            $std = in_array($config, array('full', 'medium', 'minimal', 'sidebar', ''));
            $fn = $std
                ? $pth['folder']['plugins'] . 'codeeditor/inits/init.json'
                : $config;
            $config = ($config = file_get_contents($fn)) !== false ? $config : '{}';
        }
        $parsedConfig = json_decode($config, true);
        if (!is_array($parsedConfig)) {
            $e .= '<li><b>' . $ptx['error_json'] . '</b>' . '<br>'
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
        // We set the undocumented leaveSubmitMehtodAlone option; otherwise
        // multiple editors on the same form might trigger form submission
        // multiple times.
        $config['leaveSubmitMethodAlone'] = true;
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
    protected static function filebrowser()
    {
        global $adm, $sn, $pth, $cf;

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
            $url = $sn . '?filebrowser=editorbrowser&editor=codeeditor&prefix='
                . CMSIMPLE_BASE . '&base=./&type=';
            $script = <<<EOS
codeeditor.filebrowser = function(type) {
    window.open("$url" + type, "codeeditor_filebrowser",
            "toolbar=no,location=no,status=no,menubar=no," +
            "scrollbars=yes,resizable=yes,width=640,height=480");
}
EOS;
        }
        return $script;
    }

    /**
     * Writes the basic JavaScript of the editor to the `head' element.
     * No editors are actually created. Multiple calls are allowed.
     * This is called from init_EDITOR() automatically, but not from
     * EDITOR_replace().
     *
     * @return void
     *
     * @global string (X)HTML to insert in the `head' element.
     * @global array  The paths of system files and folders.
     * @global array  The configuration of the plugins.
     * @global array  The localization of the plugins.
     */
    public static function doInclude()
    {
        global $hjs, $pth, $plugin_cf, $plugin_tx;
        static $again = false;

        if ($again) {
            return;
        }
        $again = true;

        $pcf = $plugin_cf['codeeditor'];
        $ptx = $plugin_tx['codeeditor'];
        $dir = $pth['folder']['plugins'] . 'codeeditor/';

        $css = '<link rel="stylesheet" type="text/css" href="' . $dir
            . 'codemirror/codemirror-combined.css">';
        $fn = $dir . 'codemirror/theme/' . $pcf['theme'] . '.css';
        if (file_exists($fn)) {
            $css .= '<link rel="stylesheet" type="text/css" href="' . $fn . '">';
        }
        $text = array('confirmLeave' => $ptx['confirm_leave']);
        $text = json_encode($text);
        $filebrowser = self::filebrowser();

        $hjs .= <<<EOS
$css
<script src="{$dir}codemirror/codemirror-compressed.js">
</script>
<script src="{$dir}codeeditor.min.js"></script>
<script>
codeeditor.text = $text;
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
    public static function replace($elementId, $config = '')
    {
        $config = self::config('php', $config);
        return "codeeditor.instantiate('$elementId', $config, true);";
    }

    /**
     * Instantiates the editor(s) on the textarea(s) given by $classes.
     * $config is exactly the same as for EDITOR_replace().
     *
     * @param string $classes The classes of the textarea(s) that should become
     *                        an editor instance.
     * @param string $config  The configuration string.
     * @param string $mode    The highlighting mode ('php' or 'css').
     *
     * @return void
     *
     * global string (X)HTML to insert at the bottom of the `body' element.
     */
    public static function init($classes = array(), $config = false, $mode = 'php')
    {
        global $bjs;

        self::doInclude();
        if (empty($classes)) {
            $classes = array('xh-editor');
        }
        $classes = json_encode($classes);
        $config = self::config($mode, $config);
        $bjs .= <<<EOS
<script>
CodeMirror.on(window, "load", function() {
    codeeditor.instantiateByClasses($classes, $config, true);
})
</script>

EOS;
    }

    /**
     * Returns all available themes.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     */
    public static function getThemes()
    {
        global $pth;

        $themes = array('', 'default');
        $foldername = $pth['folder']['plugins'] . 'codeeditor/codemirror/theme';
        if ($dir = opendir($foldername)) {
            while (($entry = readdir($dir)) !== false) {
                if (pathinfo($entry, PATHINFO_EXTENSION) == 'css') {
                    $themes[] = basename($entry, '.css');
                }
            }
        }
        return $themes;
    }
}
