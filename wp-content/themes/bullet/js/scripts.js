// Scripts File

/*! responsive-nav.js v1.0.20 by @viljamis, http://responsive-nav.com, MIT license */
var responsiveNav=function(h,g){var v=!!h.getComputedStyle;h.getComputedStyle||(h.getComputedStyle=function(a){this.el=a;this.getPropertyValue=function(b){var c=/(\-([a-z]){1})/g;"float"===b&&(b="styleFloat");c.test(b)&&(b=b.replace(c,function(a,b,c){return c.toUpperCase()}));return a.currentStyle[b]?a.currentStyle[b]:null};return this});var d,f,e,n=g.createElement("style"),p,q,l=function(a,b,c,d){if("addEventListener"in a)try{a.addEventListener(b,c,d)}catch(e){if("object"===typeof c&&c.handleEvent)a.addEventListener(b,
function(a){c.handleEvent.call(c,a)},d);else throw e;}else"attachEvent"in a&&("object"===typeof c&&c.handleEvent?a.attachEvent("on"+b,function(){c.handleEvent.call(c)}):a.attachEvent("on"+b,c))},k=function(a,b,c,d){if("removeEventListener"in a)try{a.removeEventListener(b,c,d)}catch(e){if("object"===typeof c&&c.handleEvent)a.removeEventListener(b,function(a){c.handleEvent.call(c,a)},d);else throw e;}else"detachEvent"in a&&("object"===typeof c&&c.handleEvent?a.detachEvent("on"+b,function(){c.handleEvent.call(c)}):
a.detachEvent("on"+b,c))},w=function(a){if(1>a.children.length)throw Error("The Nav container has no containing elements");for(var b=[],c=0;c<a.children.length;c++)1===a.children[c].nodeType&&b.push(a.children[c]);return b},m=function(a,b){for(var c in b)a.setAttribute(c,b[c])},r=function(a,b){a.className+=" "+b;a.className=a.className.replace(/(^\s*)|(\s*$)/g,"")},s=function(a,b){a.className=a.className.replace(RegExp("(\\s|^)"+b+"(\\s|$)")," ").replace(/(^\s*)|(\s*$)/g,"")},u=function(a,b){var c;
this.options={animate:!0,transition:350,label:"Menu",insert:"after",customToggle:"",openPos:"relative",jsClass:"js",init:function(){},open:function(){},close:function(){}};for(c in b)this.options[c]=b[c];r(g.documentElement,this.options.jsClass);this.wrapperEl=a.replace("#","");if(g.getElementById(this.wrapperEl))this.wrapper=g.getElementById(this.wrapperEl);else throw Error("The nav element you are trying to select doesn't exist");this.wrapper.inner=w(this.wrapper);f=this.options;d=this.wrapper;
this._init(this)};u.prototype={destroy:function(){this._removeStyles();s(d,"closed");s(d,"opened");d.removeAttribute("style");d.removeAttribute("aria-hidden");t=d=null;k(h,"resize",this,!1);k(g.body,"touchmove",this,!1);k(e,"touchstart",this,!1);k(e,"touchend",this,!1);k(e,"keyup",this,!1);k(e,"click",this,!1);k(e,"mouseup",this,!1);f.customToggle?e.removeAttribute("aria-hidden"):e.parentNode.removeChild(e)},toggle:function(){!0===p&&(q?(s(d,"opened"),r(d,"closed"),m(d,{"aria-hidden":"true"}),f.animate?
(p=!1,setTimeout(function(){d.style.position="absolute";p=!0},f.transition+10)):d.style.position="absolute",q=!1,f.close()):(s(d,"closed"),r(d,"opened"),d.style.position=f.openPos,m(d,{"aria-hidden":"false"}),q=!0,f.open()))},handleEvent:function(a){a=a||h.event;switch(a.type){case "touchstart":this._onTouchStart(a);break;case "touchmove":this._onTouchMove(a);break;case "touchend":case "mouseup":this._onTouchEnd(a);break;case "click":this._preventDefault(a);break;case "keyup":this._onKeyUp(a);break;
case "resize":this._resize(a)}},_init:function(){r(d,"closed");p=!0;q=!1;this._createToggle();this._transitions();this._resize();l(h,"resize",this,!1);l(g.body,"touchmove",this,!1);l(e,"touchstart",this,!1);l(e,"touchend",this,!1);l(e,"mouseup",this,!1);l(e,"keyup",this,!1);l(e,"click",this,!1);f.init()},_createStyles:function(){n.parentNode||g.getElementsByTagName("head")[0].appendChild(n)},_removeStyles:function(){n.parentNode&&n.parentNode.removeChild(n)},_createToggle:function(){if(f.customToggle){var a=
f.customToggle.replace("#","");if(g.getElementById(a))e=g.getElementById(a);else throw Error("The custom nav toggle you are trying to select doesn't exist");}else a=g.createElement("a"),a.innerHTML=f.label,m(a,{href:"#",id:"nav-toggle"}),"after"===f.insert?d.parentNode.insertBefore(a,d.nextSibling):d.parentNode.insertBefore(a,d),e=g.getElementById("nav-toggle")},_preventDefault:function(a){a.preventDefault?(a.preventDefault(),a.stopPropagation()):a.returnValue=!1},_onTouchStart:function(a){a.stopPropagation();
this.startX=a.touches[0].clientX;this.startY=a.touches[0].clientY;this.touchHasMoved=!1;k(e,"mouseup",this,!1)},_onTouchMove:function(a){if(10<Math.abs(a.touches[0].clientX-this.startX)||10<Math.abs(a.touches[0].clientY-this.startY))this.touchHasMoved=!0},_onTouchEnd:function(a){this._preventDefault(a);if(!this.touchHasMoved)if("touchend"===a.type){this.toggle(a);var b=this;d.addEventListener("click",b._preventDefault,!0);setTimeout(function(){d.removeEventListener("click",b._preventDefault,!0)},
f.transition+100)}else{var c=a||h.event;3!==c.which&&2!==c.button&&this.toggle(a)}},_onKeyUp:function(a){13===(a||h.event).keyCode&&this.toggle(a)},_transitions:function(){if(f.animate){var a=d.style,b="max-height "+f.transition+"ms";a.WebkitTransition=b;a.MozTransition=b;a.OTransition=b;a.transition=b}},_calcHeight:function(){for(var a=0,b=0;b<d.inner.length;b++)a+=d.inner[b].offsetHeight;a="#"+this.wrapperEl+".opened{max-height:"+a+"px}";v&&(n.innerHTML=a)},_resize:function(){"none"!==h.getComputedStyle(e,
null).getPropertyValue("display")?(m(e,{"aria-hidden":"false"}),d.className.match(/(^|\s)closed(\s|$)/)&&(m(d,{"aria-hidden":"true"}),d.style.position="absolute"),this._createStyles(),this._calcHeight()):(m(e,{"aria-hidden":"true"}),m(d,{"aria-hidden":"false"}),d.style.position=f.openPos,this._removeStyles())}};var t;return function(a,b){t||(t=new u(a,b));return t}}(window,document);

var navigation = responsiveNav("#nav");

jQuery(document).ready(function($) {

	// add all your scripts here

}); /* end of as page load scripts */


// Calculates em values from px
// use like this - (20).toEm()
jQuery.fn.toEm = function(settings){
	settings = jQuery.extend({
		scope: 'body'
	}, settings);
	var that = parseInt(this[0],10),
		scopeTest = jQuery('<div style="display: none; font-size: 1em; margin: 0; padding:0; height: auto; line-height: 1; border:0;">&nbsp;</div>').appendTo(settings.scope),
		scopeVal = scopeTest.height();
	scopeTest.remove();
	return (that / scopeVal).toFixed(8);
};

// Calculates pixel values from browser em size
// use like this - (47).toPx()
jQuery.fn.toPx = function(settings){
	settings = jQuery.extend({
		scope: 'body'
	}, settings);
	var that = parseFloat(this[0]),
		scopeTest = jQuery('<div style="display: none; font-size: 1em; margin: 0; padding:0; height: auto; line-height: 1; border:0;">&nbsp;</div>').appendTo(settings.scope),
		scopeVal = scopeTest.height();
	scopeTest.remove();
	return Math.round(that * scopeVal);
};

// HTML5 Fallbacks for older browsers
// This makes the HTML5 placeholder attribute work on form fields
jQuery(function($) {
	// check placeholder browser support
	var placeholderSupport = "placeholder" in document.createElement("input");
	if (!placeholderSupport) {
		// set placeholder values
		$(this).find('[placeholder]').each(function() {
			$(this).val( $(this).attr('placeholder') );
		});

		// focus and blur of placeholders
		$('[placeholder]').focus(function() {
			if ($(this).val() === $(this).attr('placeholder')) {
				$(this).val('');
				$(this).removeClass('placeholder');
			}
		}).blur(function() {
			if ($(this).val() === '' || $(this).val() === $(this).attr('placeholder')) {
				$(this).val($(this).attr('placeholder'));
				$(this).addClass('placeholder');
			}
		});

		// remove placeholders on submit
		$('[placeholder]').closest('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				if ($(this).val() === $(this).attr('placeholder')) {
					$(this).val('');
				}
			});
		});
	}
});

/*! A fix for the iOS orientationchange zoom bug. Script by @scottjehl, rebound by @wilto. MIT / GPLv2 License. */
(function(w){

	// This fix addresses an iOS bug, so return early if the UA claims it's something else.
	var ua = navigator.userAgent;
	if( !( /iPhone|iPad|iPod/.test( navigator.platform ) && /OS [1-5]_[0-9_]* like Mac OS X/i.test(ua) && ua.indexOf( "AppleWebKit" ) > -1 ) ){
		return;
	}

	var doc = w.document;

	if( !doc.querySelector ){ return; }

	var meta = doc.querySelector( "meta[name=viewport]" ),
		initialContent = meta && meta.getAttribute( "content" ),
		disabledZoom = initialContent + ",maximum-scale=1",
		enabledZoom = initialContent + ",maximum-scale=10",
		enabled = true,
		x, y, z, aig;

	if( !meta ){ return; }

	function restoreZoom(){
		meta.setAttribute( "content", enabledZoom );
		enabled = true;
	}

	function disableZoom(){
		meta.setAttribute( "content", disabledZoom );
		enabled = false;
	}

	function checkTilt( e ){
		aig = e.accelerationIncludingGravity;
		x = Math.abs( aig.x );
		y = Math.abs( aig.y );
		z = Math.abs( aig.z );

		// If portrait orientation and in one of the danger zones
		if( (!w.orientation || w.orientation === 180) && ( x > 7 || ( ( z > 6 && y < 8 || z < 8 && y > 6 ) && x > 5 ) ) ){
			if( enabled ){
				disableZoom();
			}
		}
		else if( !enabled ){
			restoreZoom();
		}
	}

	w.addEventListener( "orientationchange", restoreZoom, false );
	w.addEventListener( "devicemotion", checkTilt, false );

})( this );
