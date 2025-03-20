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

use Plib\SystemChecker;
use Plib\View;

class InfoCommand
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): string
    {
        return '<h1>Codeeditor ' . Plugin::VERSION . '</h1>'
            . $this->systemCheck();
    }

    private function systemCheck(): string
    {
        $phpVersion = '7.1.0';
        $o = '<h2>' . $this->view->text("syscheck_title") . '</h2>';
        $result = $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion) >= 0 ? 'success' : 'fail';
        $o .= $this->view->message($result, "syscheck_phpversion", $phpVersion);
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        foreach ($folders as $folder) {
            $result = $this->systemChecker->checkWritability($folder) ? 'success' : 'warn';
            $o .= $this->view->message($result, "syscheck_writable", $folder);
        }
        return $o;
    }
}
