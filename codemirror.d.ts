declare namespace CodeMirror {
    function on(element, event, fun);
    function off(element, event, fun);
    function fromTextArea(element, config);
    function getTextArea();
    function setOption(option, value);
    function getOption(option);
    function getWrapperElement();
    function getScrollerElement();
    function getValue();
    function foldCode(pos);
    function getCursor();
    function cmbMayPreview();
    let cmbFullscreen;
}
