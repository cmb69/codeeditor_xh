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

use Plib\Request;
use Plib\View;

class Editor
{
    /** @var string */
    private $pluginsFolder;

    /** @var string */
    private $theme;

    /** @var string */
    private $filebrowser;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginsFolder,
        string $theme,
        string $filebrowser,
        View $view
    ) {
        $this->pluginsFolder = $pluginsFolder;
        $this->theme = $theme;
        $this->filebrowser = $filebrowser;
        $this->view = $view;
    }

    private function config(string $mode, string $config): string
    {
        global $e;

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
            $e .= '<li><b>' . $this->view->text("error_json") . '</b>' . '<br>'
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

    private function filebrowser(Request $request): string
    {
        global $sn;

        // no filebrowser, if editor is called from front-end
        if (!$request->admin()) {
            return '';
        }

        $script = '';
        if ($this->filebrowser !== "") {
            $connector = $this->pluginsFolder . $this->filebrowser
                . '/connectors/codeeditor/codeeditor.php';
            if (is_readable($connector)) {
                include_once $connector;
                $init = $this->filebrowser . '_codeeditor_init';
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
    public function doInclude(Request $request)
    {
        global $hjs;

        $dir = $this->pluginsFolder . 'codeeditor/';

        $stylesheets = [$dir . "codemirror/codemirror-combined.css"];
        $fn = $dir . 'codemirror/theme/' . $this->theme . '.css';
        if (file_exists($fn)) {
            $stylesheets[] = $fn;
        }
        $text = array('confirmLeave' => $this->view->text("confirm_leave"));
        $text = json_encode($text);
        $hjs .= $this->view->render("editor", [
            "stylesheets" => $stylesheets,
            "codemirror" => $dir . "codemirror/codemirror-compressed.js",
            "codeeditor" => $dir . "codeeditor.min.js",
            "text" => $text,
            "filebrowser" => $this->filebrowser($request),
        ]);
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
    public function init(
        Request $request,
        array $classes = [],
        $config = false,
        string $mode = 'php',
        bool $mayPreview = true
    ) {
        global $bjs;

        $this->doInclude($request);
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
