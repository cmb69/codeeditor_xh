/**
 * JS of Codeeditor_XH.
 *
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


// utf-8-marker: äöüß


var codeeditor = {

    instances: new Array(),

    current: null,

    unloadHandlerAdded: false,


    onFocus: function(editor) {
	codeeditor.current = editor;
    },


    foldFunc: CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder),


//    toggleFullscreen: function() {
//	var ce = CODEEDITOR.current;
//	var scroller = ce.getScrollerElement();
//	var html = document.getElementsByTagName('html')[0];
//	if (scroller.className.search(/fullscreen/) < 0) {
//	    scroller.className += ' fullscreen';
//	    html.style.oldOverflow = html.style.overflow;
//	    html.style.overflow = 'hidden';
//	} else {
//	    scroller.className = scroller.className.replace(/fullscreen/, '');
//	    html.style.overflow = html.style.oldOverflow;
//	}
//	ce.refresh();
//    },


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
	//ta.form.onsubmit = function() {return CODEEDITOR.onSave(cm)};
	this.addUnloadHandler();

	//// toolbar
	//var tb = document.getElementById('codeeditor_toolbar').cloneNode(true);
	//tb.id = tb.id + '1'; // TODO: unique ID
	//tb.style.display = 'block';
	//var wrapper = cm.getWrapperElement();
	//wrapper.parentNode.insertBefore(tb, wrapper);
	//
	//// statusbar
	//var sb = document.getElementById('codeeditor_statusbar').cloneNode(true);
	//sb.id = sb.id + '1'; // TODO unique ID
	//sb.style.display = 'block';
	//if (wrapper.nextSibling) {
	//    wrapper.parentNode.insertBefore(sb, wrapper.nextSibling);
	//} else {
	//    wrapper.parentNode.appendChild(sb);
	//}


	//if (addSave) {
	//    var wrapper = cm.getWrapperElement();
	//    var save = document.createElement('input');
	//    with (save) {
	//	setAttribute('type', 'submit');
	//	setAttribute('class', 'submit');
	//	setAttribute('value', CODEEDITOR.text.save);
	//	style.marginTop = '1em';
	//    }
	//    if (wrapper.nextSibling) {
	//	wrapper.parentNode.insertBefore(save, wrapper.nextSibling);
	//    } else {
	//	wrapper.parentNode.appendChild(save);
	//    }
	//}

    },


//    getForm: function() {
//	var n = this.current.getWrapperElement();
//	while (n.nodeName != 'FORM') {n = n.parentNode};
//	return n;
//    },

    addUnloadHandler: function() {
	if (!codeeditor.unloadHandlerAdded) {
	    if (window.addEventListener) {
		window.addEventListener('beforeunload', this.beforeUnload, false);
	    } else {
		window.attachEvent('onbeforeunload', this.beforeUnload);
	    }
	    codeeditor.unloadHandlerAdded = true;
	}
    },


    beforeUnload: function(e) {
	for (var i = 0; i < codeeditor.instances.length; i++) {
	    if (codeeditor.instances[i].historySize().undo > 0) {
		return e.returnValue = codeeditor.text.confirmLeave;
	    }
	}
    },


//    onSave: function(editor) {
//	if (editor.historySize().undo == 0) {
//	    alert(CODEEDITOR.text.noChanges);
//	    return false;
//	} else {
//	    if (window.addEventListener) {
//		window.removeEventListener('beforeunload', this.beforeUnload, false);
//	    } else {
//		window.detachEvent('onbeforeunload', this.beforeUnload);
//	    }
//	    return true;
//	}
//    },


//    undo: function() {
//	CODEEDITOR.current.undo();
//    },
//
//
//    redo: function() {
//	CODEEDITOR.current.redo();
//    },


    insertImage: function(url) {
	this.current.replaceSelection('<img src="' + url + '" alt=""'
		+ (this.xhtml ? ' />' : '>'));
	this.current.focus();
    },

    insertLink: function(url) {
	this.current.replaceSelection('<a href="' + url + '">' + this.current.getSelection() + '</a>');
	this.current.focus();
    },
    
    insertURI: function(url) {
	this.current.replaceSelection(url);
	this.current.focus();
    },

//    onCursorActivity: function(cm) {
//	var pos = cm.coordsChar(cm.cursorCoords(true));
//	var txt = 'Line: ' + pos.line + ' Col: ' + pos.ch;
//	cm.getWrapperElement().nextSibling.innerHTML = txt;
//    }
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
    function onSave(cm) {
	if (cm.historySize().undo == 0) {
	    alert(codeeditor.text.noChanges);
	    return false;
	} else {
	    if (window.addEventListener) {
		window.removeEventListener('beforeunload', codeeditor.beforeUnload, false);
	    } else {
		window.detachEvent('onbeforeunload', codeeditor.beforeUnload);
	    }
	    return true;
	}
    }
    function getForm() {
	var n = cm.getWrapperElement();
	while (n.nodeName != 'FORM') {n = n.parentNode};
	return n;
    }
    if (onSave(cm)) {
	getForm().submit();
    }
}
