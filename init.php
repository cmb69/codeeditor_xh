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

/**
 * Writes the basic JavaScript of the editor to the `head' element.
 * No editors are actually created. Multiple calls are allowed.
 * This is called from init_EDITOR() automatically, but not from EDITOR_replace().
 *
 * @return void
 */
function include_codeeditor()
{
    Codeeditor\Plugin::doInclude();
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
function codeeditor_replace($elementId, $config = '')
{
    return Codeeditor\Plugin::replace($elementId, $config);
}

/**
 * Instantiates the editor(s) on the textarea(s) given by $classes.
 * $config is exactly the same as for EDITOR_replace().
 *
 * @param array<int,string> $classes The classes of the textarea(s) that should become
 *                                   an editor instance.
 * @param string|false $config       The configuration string.
 *
 * @return bool
 */
function init_codeeditor($classes = array(), $config = false)
{
    Codeeditor\Plugin::init($classes, $config);
    return true;
}

/**
 * Instantiates the editor(s) in CSS mode on the textarea(s) given by $classes.
 * $config is exactly the same as for EDITOR_replace().
 *
 * @param array<int,string> $classes The classes of the textarea(s) that should become
 *                                   an editor instance.
 * @param string|false $config       The configuration string.
 *
 * @return bool
 */
function init_codeeditor_css($classes = array(), $config = false)
{
    Codeeditor\Plugin::init($classes, $config, 'css');
    return true;
}
