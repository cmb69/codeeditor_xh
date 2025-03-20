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

class MainCommand
{
    /**
     * @return void
     */
    public function __invoke()
    {
        if ($this->isEditingPhp()) {
            $mode = 'php';
            $class = 'xh_file_edit';
        } elseif ($this->isEditingCss()) {
            $mode = 'css';
            $class = 'xh_file_edit';
        } else {
            return;
        }
        Plugin::init([$class], '', $mode, false);
    }

    private function isEditingPhp(): bool
    {
        global $action, $file;

        return $file == 'template' && ($action == 'edit' || $action == '')
            || $file == 'content' && ($action == 'edit' || $action == '');
    }

    private function isEditingCss(): bool
    {
        global $admin, $action, $file;

        return $file == 'stylesheet' && ($action == 'edit' || $action == '')
            || $admin == 'plugin_stylesheet' && $action == 'plugin_text';
    }
}
