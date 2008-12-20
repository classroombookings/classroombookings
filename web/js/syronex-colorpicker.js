/**
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * (C) 2008 Syronex / J.M. Rosengard
 * http://www.syronex.com/software/jquery-color-picker
 *
 * - Check mark is either black or white, depending on the darkness 
 *   of the color selected.
 * - Fixed a bug in the original plugin that led to problems when there is 
 *   more than one colorPicker in a document.
 *
 * This is based on: 
 *
 * jQuery colorSelect plugin 0.9
 * http://plugins.jquery.com/project/colorPickerAgain
 * Copyright (c) 2008 Otaku RzO (Renzo Galo Castro Jurado).
 * (Original author URL & domain name no longer available.)
 *
 */

 
(function($) {
	$.fn.colorPicker = function($$options) {
		// Defaults
		var $defaults = {
			color:new Array(
				"#FCE94F", "#EDD400", "#C4A000",
				"#FCAF3E", "#F57900", "#CE5C00",
				"#E9B96E", "#C17D11", "#8F5902",
				"#8AE234", "#73D216", "#4E9A06",
				"#729FCF", "#3465A4", "#204A87",
				"#AD7FA8", "#75507B", "#5C3566",
				"#EF2929", "#CC0000", "#A40000",
				"#EEEEEC", "#BABDB6", "#2E3436"
			),
			defaultColor: 0,
			columns: 6,
			click: function($color){}
		};
		
		var $settings = $.extend({}, $defaults, $$options);
		
		// Iterate and reformat each matched element
		return this.each(function() {
			var $this = $(this);
			
			// build element specific options
			var o = $.meta ? $.extend({}, $settings, $this.data()) : $settings;
			o.defaultColor = o.color.indexOf(o.activeColour);
			
			var $$oldIndex = typeof(o.defaultColor)=='number' ? o.defaultColor : -1;
			
			
			var _html = "";
			for(i=0;i<o.color.length;i++){
				_html += '<div style="background-color:'+o.color[i]+';"></div>';
				if($$oldIndex==-1 && o.defaultColor==o.color[i]) $$oldIndex = i;
			}
			
			$this.html('<div class="jColorSelect">'+_html+'</div>');
			var $color = $this.children('.jColorSelect').children('div');
			// Set container width
			var w = ($color.width()+2+2) * (o.columns>0 ? o.columns : o.color.length );
			$this.children('.jColorSelect').css('width',w);
			
			// Subscribe to click event of each color box
			$color.each(function(i){
				$(this).click(function(){
					if( $$oldIndex == i ) return;	  
					if( $$oldIndex > -1 ){
						cell = $color.eq($$oldIndex);
						if(cell.hasClass('check')) cell.removeClass('check').removeClass('checkwht').removeClass('checkblk');
					}
					// Keep index
					$$oldIndex = i;
					$(this).addClass('check').addClass(isdark(o.color[i]) ? 'checkwht' : 'checkblk');
					// Trigger user event
					o.click(o.color[i]);
				});
			});
			
			// Simulate click for defaultColor
			_tmp = $$oldIndex;
			$$oldIndex = -1;
			$color.eq(_tmp).trigger('click');
		});    
		
		return this;
		
	};
	
})(jQuery);


/**
 * Return true if color is dark, false otherwise.
 * (C) 2008 Syronex / J.M. Rosengard
 **/
function isdark(color){
	var colr = parseInt(color.substr(1), 16);
	return (colr >>> 16) // R
    + ((colr >>> 8) & 0x00ff) // G 
    + (colr & 0x0000ff) // B
    < 500;
}