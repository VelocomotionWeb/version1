/**
 * jQuery alterClass plugin
 *
 * Remove element classes with wildcard matching. Optionally add classes:
 *   $( '#foo' ).alterClass( 'foo-* bar-*', 'foobar' )
 *
 * Copyright (c) 2011 Pete Boere (the-echoplex.net)
 * Free under terms of the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 */
(function ( $ ) {

$.fn.alterClass = function ( removals, additions ) {

	var self = this;

	if ( removals.indexOf( '*' ) === -1 ) {
		// Use native jQuery methods if there is no wildcard matching
		self.removeClass( removals );
		return !additions ? self : self.addClass( additions );
	}

	var patt = new RegExp( '\\s' +
			removals.
				replace( /\*/g, '[A-Za-z0-9-_]+' ).
				split( ' ' ).
				join( '\\s|\\s' ) +
			'\\s', 'g' );

	self.each( function ( i, it ) {
		var cn = ' ' + it.className + ' ';
		while ( patt.test( cn ) ) {
			cn = cn.replace( patt, ' ' );
		}
		it.className = $.trim( cn );
	});

	return !additions ? self : self.addClass( additions );
};
/*-------------------------------------------------------------------- 
 * jQuery pixel/em conversion plugins: toEm() and toPx()
 * by Scott Jehl (scott@filamentgroup.com), http://www.filamentgroup.com
 * Copyright (c) Filament Group
 * Dual licensed under the MIT (filamentgroup.com/examples/mit-license.txt) or GPL (filamentgroup.com/examples/gpl-license.txt) licenses.
 * Article: http://www.filamentgroup.com/lab/update_jquery_plugin_for_retaining_scalable_interfaces_with_pixel_to_em_con/
 * Options:  	 								
 		scope: string or jQuery selector for font-size scoping		  
 * Usage Example: $(myPixelValue).toEm(); or $(myEmValue).toPx();
--------------------------------------------------------------------*/

$.fn.toEm = function(settings){
	settings = jQuery.extend({
		scope: 'body'
	}, settings);
	var that = parseInt(this[0],10),
		scopeTest = jQuery('<div style="display: none; font-size: 1em; margin: 0; padding:0; height: auto; line-height: 1; border:0;">&nbsp;</div>').appendTo(settings.scope),
		scopeVal = scopeTest.height();
	scopeTest.remove();
	return (that / scopeVal).toFixed(8) + 'em';
};


$.fn.toPx = function(settings){
	settings = jQuery.extend({
		scope: 'body'
	}, settings);
	var that = parseFloat(this[0]),
		scopeTest = jQuery('<div style="display: none; font-size: 1em; margin: 0; padding:0; height: auto; line-height: 1; border:0;">&nbsp;</div>').appendTo(settings.scope),
		scopeVal = scopeTest.height();
	scopeTest.remove();
	return Math.round(that * scopeVal) + 'px';
};
})( jQuery );

function colorToRgba(e, alpha) { //e = jQuery element, alpha = background-opacity
    var b = e.css('backgroundColor'),
    	response = '',
    	alpha = (alpha >0 ? alpha/100 : 0);
    ;

    if(alpha && b.match(/,/g)){
    	response =	'rgba' + b.slice(b.indexOf('('), ( (b.match(/,/g).length == 2) ? -1 : b.lastIndexOf(',') - b.length) ) + ', '+alpha+')';
    }
    
    return response
};
function removeTinyUniqueId(selector) {
     selector = (selector ? selector : "*");
	return $(selector).each(function() {
		if ( /^mce_\d+$/.test( this.id ) ) {
			$( this ).removeAttr( "id" );
		}
	});
};
function removeUiClass(selector) {
    selector = (selector ? selector : "*");
		$( selector).alterClass( "ui-*",'' );
};
//replaceElementTag('span', '<div></div>');
function replaceElementTag(targetSelector, newTagString) {
	$(targetSelector).each(function(){
		var newElem = $(newTagString, {html: $(this).html()});
		$.each(this.attributes, function() {
			newElem.attr(this.name, this.value);
		});
		$(this).replaceWith(newElem);
	});
};

function getRotationDegrees(obj) {
        var matrix = obj.css("-webkit-transform") ||
        obj.css("-moz-transform")    ||
        obj.css("-ms-transform")     ||
        obj.css("-o-transform")      ||
        obj.css("transform");
        if(matrix !== 'none') {
            var values = matrix.split('(')[1].split(')')[0].split(',');
            var a = values[0];
            var b = values[1];
            var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
        } else { var angle = 0; }
        //return (angle < 0) ? angle + 360 : angle;
         return angle;
    }
function getOpacity(el) {
	var result = parseFloat(el.css('background-color').split(',')[3]);

  return (isNaN(result) ? 100 : (result*100));
}
function SortByName(a, b){
  var aName = a.name.toLowerCase();
  var bName = b.name.toLowerCase(); 
  return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
}