<?php

/**
 * Copyright (c) Christoph M. Becker
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

class Editor
{
    /** @var string */
    private $pluginsFolder;

    /** @var string */
    private $theme;

    public function __construct(string $pluginsFolder, string $theme)
    {
        $this->pluginsFolder = $pluginsFolder;
        $this->theme = $theme;
    }

    private function config(string $mode, string $config): string
    {
        global $e, $plugin_tx;

        $ptx = $plugin_tx['codeeditor'];
        $config = trim($config);
        if (empty($config) || $config[0] != '{') {
            $std = in_array($config, array('full', 'medium', 'minimal', 'sidebar', ''));
            $fn = $std
                ? $this->pluginsFolder . 'codeeditor/inits/init.json'
                : $config;
            $config = ($config = file_get_contents($fn)) !== false ? $config : '{}';
        }
        $parsedConfig = json_decode($config, true);
        if (!is_array($parsedConfig)) {
            $e .= '<li><b>' . $ptx['error_json'] . '</b>' . '<br>'
                . (isset($fn) ? $fn : htmlspecialchars($config, ENT_QUOTES, 'UTF-8'))
                . '</li>';
            return "";
        }
        $config = $parsedConfig;
        if (!isset($config['mode']) || $config['mode'] == '%MODE%') {
            $config['mode'] = $mode;
        }
        if (!isset($config['theme']) || $config['theme'] == '%THEME%') {
            $config['theme'] = $this->theme;
        }
        // We set the undocumented leaveSubmitMehtodAlone option; otherwise
        // multiple editors on the same form might trigger form submission
        // multiple times.
        $config['leaveSubmitMethodAlone'] = true;
        $config = (string) json_encode($config);
        return $config;
    }

    private function filebrowser(): string
    {
        global $adm, $sn, $cf;

        // no filebrowser, if editor is called from front-end
        if (!$adm) {
            return '';
        }

        $script = '';
        if (!empty($cf['filebrowser']['external'])) {
            $connector = $this->pluginsFolder . $cf['filebrowser']['external']
                . '/connectors/codeeditor/codeeditor.php';
            if (is_readable($connector)) {
                include_once $connector;
                $init = $cf['filebrowser']['external'] . '_codeeditor_init';
                if (is_callable($init)) {
                    $script = $init();
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
     * @return void
     */
    public function doInclude()
    {
        global $hjs, $plugin_tx;
        static $again = false;

        if ($again) {
            return;
        }
        $again = true;

        $ptx = $plugin_tx['codeeditor'];
        $dir = $this->pluginsFolder . 'codeeditor/';

        $css = '<link rel="stylesheet" type="text/css" href="' . $dir
            . 'codemirror/codemirror-combined.css">';
        $fn = $dir . 'codemirror/theme/' . $this->theme . '.css';
        if (file_exists($fn)) {
            $css .= '<link rel="stylesheet" type="text/css" href="' . $fn . '">';
        }
        $text = array('confirmLeave' => $ptx['confirm_leave']);
        $text = json_encode($text);
        $filebrowser = $this->filebrowser();

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

    public function replace(string $elementId, string $config = ''): string
    {
        $config = $this->config('php', $config);
        return "codeeditor.instantiate('$elementId', $config, true);";
    }

    /**
     * @param array<int,string> $classes
     * @param string|false $config
     * @return void
     */
    public function init(array $classes = [], $config = false, string $mode = 'php', bool $mayPreview = true)
    {
        global $bjs;

        $this->doInclude();
        if (empty($classes)) {
            $classes = array('xh-editor');
        }
        $classes = json_encode($classes);
        $config = $this->config($mode, (string) $config);
        $mayPreview = json_encode($mayPreview);
        $bjs .= <<<EOS
<script>
CodeMirror.on(window, "load", function() {
    codeeditor.instantiateByClasses($classes, $config, $mayPreview);
})
</script>

EOS;
    }

    /**
     * @return array<int,string>
     */
    public function getThemes(): array
    {
        $themes = array('', 'default');
        $foldername = $this->pluginsFolder . 'codeeditor/codemirror/theme';
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
