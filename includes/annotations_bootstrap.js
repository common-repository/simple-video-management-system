
(function(){
window['google'] = window['google'] || {};
window['google']['annotations'] = window['google']['annotations'] || {};
if (!window['google']['annotations']['loaded']) {
window['google']['annotations']['loaded'] = true;
window['google']['annotations']['callback'] = function() {
google.annotations.setAbsoluteUriPrefix("https://www.google.com/");
};
document.write('<script type="text/javascript" src="annotations_production.js"></script>');
}
})()
