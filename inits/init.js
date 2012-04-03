{
    mode: '%MODE%',
    theme: 'default',
    indentUnit: 4,
    tabSize: 8,
    indentWithTabs: true,
    tabMode: 'shift',
    electricChars: true,
    extraKeys: {
        'Esc': 'toggleFullscreen',
        'Ctrl-Q': function(cm) {CODEEDITOR.foldFunc(cm, cm.getCursor().line);},
        'Ctrl-I': function(cm) {CODEEDITOR.filebrowser('images')},
        'Ctrl-L': function(cm) {CODEEDITOR.filebrowser('downloads')},
        'Alt-W': function(cm) {cm.setOption('lineWrapping', !cm.getOption('lineWrapping'))}
    },
    lineWrapping: true,
    lineNumbers: true,
    firstLineNumber: 1,
    matchBrackets: true,
    workTime: 200,
    workDelay: 300,
    onFocus: CODEEDITOR.onFocus,
    undoDepth: 40
}