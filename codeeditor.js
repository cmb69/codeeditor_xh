/**
 * JS of Codeeditor_XH.
 *
 * @copyright	Copyright (c) 2012-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Codeeditor_XH
 */


/**
 * The namespace.
 */
var codeeditor = {}


/**
 * The codemirror instances of the current document.
 */
codeeditor.instances = [];


/**
 * The currently active codemirror.
 */
codeeditor.current = null;


/**
 * Register an event listener in a portable way.
 *
 * @param {EventTarget} target
 * @param {DOMString} type
 * @param {EventListener} listener
 * @returns {void}
 */
codeeditor.addEventListener = function(target, type, listener) {
    if (typeof target.addEventListener !== "undefined") {
        target.addEventListener(type, listener, false);
    } else if (typeof target.attachEvent !== "undefined") {
        target.detachEvent("on" + type, listener);
        target.attachEvent("on" + type, listener);
    }
}


/**
 * Unregisters an event listener in a portable way.
 *
 * @param {EventTarget} target
 * @param {DOMString} type
 * @param {EventListener} listener
 * @returns {void}
 */
codeeditor.removeEventListener = function(target, type, listener) {
    if (typeof target.removeEventListener !== "undefined") {
        target.removeEventListener(type, listener, false);
    } else if (typeof target.detachEvent !== "undefined") {
        target.detachEvent("on" + type, listener);
    }
}


/**
 * Toggles the folding of the code.
 */
codeeditor.fold = CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder);


/**
 * Returns all `textarea' elements with a certain class.
 *
 * @returns {Array}
 */
codeeditor.getTextareasByClass = function(name) {
    var textareas = document.getElementsByTagName('textarea'),
	pattern = new RegExp('(^|\\s)' + name + '(\\s|$)'),
    	result = [], length, i;

    for (i = 0, length = textareas.length; i < length; i++) {
	textarea = textareas[i];
	if (pattern.test(textarea.className)) {
	    result.push(textarea);
	}
    }
    return result;
}


/**
 * Returns an id, which is not used in the document already.
 *
 * @returns {String}
 */
codeeditor.uniqueId = function() {
    var prefix = 'codeeditor', i;

    while (document.getElementById(prefix + i) !== null) {
	i++;
    }
    return prefix + i;
}


/**
 * Sets the class of textarea[name=text] to cmsimplecore_file_edit
 * for compatibility with CMSimple_XH < 1.4(?)
 *
 * @returns {undefined}
 */
codeeditor.fixMissingClass = function() {
    var elements = document.getElementsByName('text'), i, length, element = null;

    for (i = 0, length = elements.length; i < length; i++) {
	element = elements[i];
	if (element.nodeName == 'TEXTAREA' && element.className == '') {
	    element.className = 'cmsimplecore_file_edit';
	}
    }
}


/**
 * Returns whether the `form' element has an element for submitting.
 *
 * @param {HTMLFormElement} form
 * @returns {Boolean}
 */
codeeditor.hasSubmit = function(form) {
    var elements, count, i, element;

    elements = form.elements;
    for (i = 0, count = elements.length; i < count; i++) {
	element = elements[i];
	if (element.type == "submit") {
	    return true;
	}
    }
    return false;
}


/**
 * Asks to stay on the page, when modifications were made.
 *
 * @param {Event} e
 * @returns {mixed}
 */
codeeditor.beforeUnload = function(e) {
    var i, count;

    for (i = 0, count = codeeditor.instances.length; i < count; i++) {
	if (codeeditor.instances[i].historySize().undo > 0) {
	    return e.returnValue = codeeditor.text.confirmLeave;
	}
    }
    return null;
}


/**
 * Inserts an URL to the current codemirror.
 *
 * To be called from the filebrowser.
 *
 * @param {String} url
 * @returns {undefined}
 */
codeeditor.insertURI = function(url) {
    var cm = codeeditor.current;

    cm.replaceSelection(url);
    cm.focus();
}


/**
 * Makes all `textarea' elements with certain classes to CodeMirrors.
 *
 * @param {Array} classes
 * @param {Object} config
 * @returns {undefined}
 */
codeeditor.instantiateByClasses = function(classes, config) {
    var classCount, i, textareas, textareaCount, j, textarea;

    for (i = 0, classCount = classes.length; i < classCount; i++) {
	textareas = codeeditor.getTextareasByClass(classes[i]);
	for (j = 0, textareaCount = textareas.length; j < textareaCount; j++) {
	    textarea = textareas[i];
	    if (!textarea.id) {
		textarea.id = codeeditor.uniqueId();
	    }
	    codeeditor.instantiate(textarea.id, config);
	}
    }
}


/**
 * Makes a `textarea' element to a CodeMirror.
 *
 * @param {String} id
 * @param {Object} config
 * @returns {undefined}
 */
codeeditor.instantiate = function(id, config) {
    var textarea = document.getElementById(id),
    	height = textarea.offsetHeight,
    	cm = CodeMirror.fromTextArea(textarea, config);

    cm.setSize(null, height);
    cm.setOption("onFocus", function(editor) {
	codeeditor.current = editor;
    });
    cm.refresh();
    codeeditor.instances.push(cm);
    codeeditor.addEventListener(textarea.form, "submit", function() {
	CodeMirror.commands.save(cm)
    });
    codeeditor.addEventListener(window, "beforeunload",
				codeeditor.beforeUnload);

}


/**
 * Triggers the submission of the `form' which contains the CodeMirror instance.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.save = function(cm) {
    var node;

    codeeditor.removeEventListener(window, "beforeunload",
				   codeeditor.beforeUnload);
    cm.save();
    node = cm.getWrapperElement();
    while (node.nodeName != 'FORM') {
	node = node.parentNode;
    }
    if (!codeeditor.hasSubmit(node)) {
	node.submit();
    }
}


/**
 * Toggles full screen mode.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toggleFullscreen = function(cm) {
    var scroller = cm.getScrollerElement();
    var body = document.body;
    if (scroller.className.indexOf("fullscreen") < 0) {
	scroller.className += " fullscreen";
	cm.cmbOldOverflow = body.style.overflow;
	body.style.overflow = "hidden";
    } else {
	scroller.className = scroller.className.replace(/fullscreen/, "");
	body.style.overflow = cm.cmbOldOverflow;
    }
    cm.refresh();
}


/**
 * Toggles the preview.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.togglePreview = function(cm) {
    var wrapper = cm.getWrapperElement(), preview;

    if (wrapper.previousSibling
	&& wrapper.previousSibling.className.indexOf("codeeditor_preview") >= 0)
    {
	preview = wrapper.previousSibling;
	preview.parentNode.removeChild(preview);
	cm.setOption("onBlur", null);
	if (cm.cmbFullscreen) {
	    delete cm.cmbFullscreen;
	    CodeMirror.commands.toggleFullscreen(cm);
	}
    } else {
	if (cm.getScrollerElement().className.indexOf("fullscreen") >= 0) {
	    CodeMirror.commands.toggleFullscreen(cm);
	    cm.cmbFullscreen = true;
	}
	preview = document.createElement("div");
	preview.className = "codeeditor_preview";
        preview.innerHTML = cm.getValue();
	wrapper.parentNode.insertBefore(preview, wrapper);
	cm.setOption("onBlur", function() {
	    CodeMirror.commands.togglePreview(cm);
	});
    }
}


/**
 * Toggles the folding of the code.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toggleFolding = function(cm) {
    codeeditor.fold(cm, cm.getCursor().line);
}


/**
 * Toggles the line wrapping.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.toogleLineWrapping = function(cm) {
    cm.setOption('lineWrapping', !cm.getOption('lineWrapping'));
}


/**
 * Opens the filebrowser for the image folder.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseImages = function(cm) {
    codeeditor.filebrowser('images');
}


/**
 * Opens the filebrowser for the downloads folder.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseDownloads = function(cm) {
    codeeditor.filebrowser('downloads');
}


/**
 * Opens the filebrowser for the media folder.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseMedia = function(cm) {
    codeeditor.filebrowser('media');
}


/**
 * Opens the filebrowser for the userfiles folder.
 *
 * @param {CodeMirror} cm
 * @returns {undefined}
 */
CodeMirror.commands.browseUserfiles = function(cm) {
    codeeditor.filebrowser('userfiles');
}
