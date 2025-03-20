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

use Plib\View;

class InfoCommand
{
    /** @var View */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function __invoke(): string
    {
        return '<h1>Codeeditor ' . Plugin::VERSION . '</h1>'
            . $this->systemCheck();
    }

    private function systemCheck(): string
    {
        global $pth;

        $phpVersion = '7.1.0';
        $o = '<h2>' . $this->view->text("syscheck_title") . '</h2>';
        $result = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'success' : 'fail';
        $o .= XH_message($result, $this->view->text("syscheck_phpversion"), $phpVersion);
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'codeeditor/' . $folder;
        }
        foreach ($folders as $folder) {
            $result = is_writable($folder) ? 'success' : 'warn';
            $o .= XH_message($result, $this->view->text("syscheck_writable"), $folder);
        }
        return $o;
    }
}
