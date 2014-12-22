<?php

/**
 * General editor interface of Codeeditor_XH.
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

require_once $pth['folder']['plugin_classes'] . 'Controller.php';

/**
 * Writes the basic JavaScript of the editor to the `head' element.
 * No editors are actually created. Multiple calls are allowed.
 * This is called from init_EDITOR() automatically, but not from EDITOR_replace().
 *
 * @return void
 */
// @codingStandardsIgnoreStart
function include_codeeditor()
{
// @codingStandardsIgnoreEnd
    Codeeditor_Controller::doInclude();
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
// @codingStandardsIgnoreStart
function codeeditor_replace($elementId, $config = '')
{
// @codingStandardsIgnoreEnd
    return Codeeditor_Controller::replace($elementId, $config);
}

/**
 * Instantiates the editor(s) on the textarea(s) given by $classes.
 * $config is exactly the same as for EDITOR_replace().
 *
 * @param string $classes The classes of the textarea(s) that should become
 *                        an editor instance.
 * @param string $config  The configuration string.
 *
 * @return void
 *
 * global string (X)HTML to insert at the bottom of the `body' element.
 */
// @codingStandardsIgnoreStart
function init_codeeditor($classes = array(), $config = false)
{
// @codingStandardsIgnoreEnd
    return Codeeditor_Controller::init($classes, $config);
}

/*
 * Include config and language file, if not yet done.
 */
global $sl; // file can be included from within a function
if (!isset($cf['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/config/config.php';
}
if (!isset($tx['codeeditor'])) {
    include $pth['folder']['plugins'] . 'codeeditor/languages/' . $sl . '.php';
}

?>
