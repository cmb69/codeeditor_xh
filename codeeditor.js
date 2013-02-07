/**
 * JS of Codeeditor_XH.
 *
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


var codeeditor = {

    instances: new Array(),

    current: null,

    unloadHandlerAdded: false,

    onFocus: function(editor) {
	codeeditor.current = editor;
    },

    foldFunc: CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder),

    getTextareasByClass: function(name) {
        var textareas = document.getElementsByTagName('textarea');
        var pattern = new RegExp('(^|\\s)' + name + '(\\s|$)');
        var res = new Array();
        for (var i = 0, j = 0; i < textareas.length; i++) {
            if (pattern.test(textareas[i].className)) {
                res[j++] = textareas[i];
            }
        }
        return res;
    },

    uniqueId: function() {
        var id = 'codeeditor';
        var i = 0;
        while (document.getElementById(id + i) !== null) {i++}
        return id + i;
    },

    setClass: function() {
	var elts = document.getElementsByName('text'), i, elt = null;
	for (i = 0; i < elts.length; i++) {
	    if (elts[i].nodeName == 'TEXTAREA' && elts[i].className == '') {
		elt = elts[i];
		break;
	    }
	}
	if (elt !== null) {
	    elt.className = 'cmsimplecore_file_edit';
	}

    },

    instantiateByClasses: function(classes, config, addSave) {
        classes = classes.split('|');
        for (var i = 0; i < classes.length; i++) {
            var textareas = this.getTextareasByClass(classes[i]);
            for (var j = 0; j < textareas.length; j++) {
                if (!textareas[j].getAttribute('id')) {
                    textareas[j].setAttribute('id', this.uniqueId());
                }
                this.instantiate(textareas[j].getAttribute('id'), config, addSave);
            }
        }
    },

    instantiate: function(id, config, addSave) {
        var ta = document.getElementById(id);
	var h = ta.offsetHeight;
        var cm = CodeMirror.fromTextArea(ta, config);
	cm.getScrollerElement().style.height = h + 'px';
	cm.refresh();
	codeeditor.instances.push(cm);
	codeeditor.addEventListener(ta.form, "submit", function() {CodeMirror.commands.save(cm)});
	this.addUnloadHandler();
    },

    addUnloadHandler: function() {
	if (!codeeditor.unloadHandlerAdded) {
	    codeeditor.addEventListener(window, "beforeunload", this.beforeUnload);
	    codeeditor.unloadHandlerAdded = true;
	}
    },

    beforeUnload: function(e) {
	for (var i = 0; i < codeeditor.instances.length; i++) {
	    if (codeeditor.instances[i].historySize().undo > 0) {
		return e.returnValue = codeeditor.text.confirmLeave;
	    }
	}
	return null;
    },

    insertURI: function(url) {
	this.current.replaceSelection(url);
	this.current.focus();
    },


    hasSubmit: function(form) {
	var elts, i, elt;

	elts = form.elements;
	for (i = 0; i < elts.length; i++) {
	    elt = elts[i];
	    if (elt.type == "submit") {
		return true;
	    }
	}
	return false;
    },

    addEventListener: function(obj, event, handler) {
	if (obj.addEventListener) {
	    obj.addEventListener(event, handler, false);
	} else {
	    obj.attachEvent("on" + event, handler);
	}
    }

}

CodeMirror.commands.toggleFullscreen = function(cm) {
    var scroller = cm.getScrollerElement();
    var html = document.getElementsByTagName('html')[0];
    if (scroller.className.search(/fullscreen/) < 0) {
	scroller.className += ' fullscreen';
	html.style.oldOverflow = html.style.overflow;
	html.style.overflow = 'hidden';
    } else {
	scroller.className = scroller.className.replace(/fullscreen/, '');
	html.style.overflow = html.style.oldOverflow;
    }
    cm.refresh();
}


CodeMirror.commands.save = function(cm) {
    var form;

    function onSave(cm) {
	if (window.addEventListener) {
	    window.removeEventListener('beforeunload', codeeditor.beforeUnload, false);
	} else {
	    window.detachEvent('onbeforeunload', codeeditor.beforeUnload);
	}
    }
    function getForm() {
	var n = cm.getWrapperElement();
	while (n.nodeName != 'FORM') {n = n.parentNode};
	return n;
    }

    onSave(cm);
    cm.save();
    form = getForm();
    if (!codeeditor.hasSubmit(form)) {
	form.submit();
    }
}
