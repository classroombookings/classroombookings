/*
 * Control.ColorPicker
 * 
 * Transforms an ordinary input textbox into an interactive color chooser,
 * allowing the user to select a color from a swatch palette.
 *
 * Features:
 *  - Allows saving custom colors to the palette for later use
 *  - Customizable by CSS
 *
 * Written and maintained by Jeremy Jongsma (jeremy@jongsma.org)
 */
if (window.Control == undefined) Control = {};

Control.ColorPicker = Class.create();
Control.ColorPicker.prototype = {
	initialize: function (element, options) {
		this.element = $(element);
		this.options = Object.extend({
				className: 'colorpickerControl'
			}, options || {});
		this.colorpicker = new Control.ColorPickerPanel({
				onSelect: this.colorSelected.bind(this)
			});

		this.dialogOpen = false;
		this.element.maxLength = 7;

		this.dialog = document.createElement('div');
		this.dialog.style.position = 'absolute';
		var cpCont = document.createElement('div');
		cpCont.className = this.options.className;
		cpCont.appendChild(this.colorpicker.element);
		this.dialog.appendChild(cpCont);

		var cont = new Element('div', {'style': 'position: relative;'});
		this.element.parentNode.replaceChild(cont, this.element);
		cont.appendChild(this.element);

		this.swatch = document.createElement('div');
		var top = '3px';
		var size = (this.element.offsetHeight - 8);
		var left = (this.element.offsetLeft + this.element.offsetWidth - (size + 5)) + 'px';
		Element.setStyle(this.swatch, {'border': '1px solid gray', 'position': 'absolute', 'left': left, 'top': top, 'fontSize': '1px', 'width': size + 'px', 'height': size + 'px', 'backgroundColor': this.element.value});
		this.swatch.title = 'Open color palette';
		this.swatch.className = 'inputExtension';
		cont.appendChild(this.swatch);

		this.element.onchange = this.textChanged.bindAsEventListener(this);
		this.element.onblur = this.hidePicker.bindAsEventListener(this);
		this.swatch.onclick = this.togglePicker.bindAsEventListener(this);
		this.documentClickListener = this.documentClickHandler.bindAsEventListener(this);
	},
	colorSelected: function(color) {
		this.element.value = color;
		this.swatch.style.backgroundColor = color;
		this.hidePicker();
	},
	textChanged: function(e) {
		this.swatch.style.backgroundColor = this.element.value;
	},
	togglePicker: function(e) {
		if (this.dialogOpen) this.hidePicker();
		else this.showPicker();
	},
	showPicker: function(e) {
		if (!this.dialogOpen) {
			var dim = Element.getDimensions(this.element);
			var position = Position.cumulativeOffset(this.element);
			var pickerTop = /MSIE/.test(navigator.userAgent) ? (position[1] + dim.height) + 'px' : (position[1] + dim.height - 1) + 'px';
			this.dialog.style.top = pickerTop;
			this.dialog.style.left = position[0] + 'px';
			document.body.appendChild(this.dialog);
			Event.observe(document, 'click', this.documentClickListener);
			this.dialogOpen = true;
		}
	},
	hidePicker: function(e) {
		if (this.dialogOpen) {
			Event.stopObserving(document, 'click', this.documentClickListener);
			Element.remove(this.dialog);
			this.dialogOpen = false;
		}
	},
	documentClickHandler: function(e) {
		var element = Event.element(e);
		var abort = false;
		do {
			if (element == this.swatch || element == this.dialog)
				abort = true;
		} while (element = element.parentNode);
		if (!abort)
			this.hidePicker();
	}
};

Control.ColorPickerPanel = Class.create();
Control.ColorPickerPanel.prototype = {

	initialize: function(options) {
		this.options = Object.extend({
				addLabel: 'Add',
				colors: Array(
					'#000000', '#993300', '#333300', '#003300', '#003366', '#000080', '#333399', '#333333',
					'#800000', '#FF6600', '#808000', '#008000', '#008080', '#0000FF', '#666699', '#808080',
					'#FF0000', '#FF9900', '#99CC00', '#339966', '#33CCCC', '#3366FF', '#800080', '#969696',
					'#FF00FF', '#FFCC00', '#FFFF00', '#00FF00', '#00FFFF', '#00CCFF', '#993366', '#C0C0C0',
					'#FF99CC', '#FFCC99', '#FFFF99', '#CCFFCC', '#CCFFFF', '#99CCFF', '#CC99FF', '#FFFFFF'),
				onSelect: Prototype.emptyFunction
			}, options || {});
		this.activeCustomSwatch =  null,
		this.customSwatches = [];

		this.element = this.create();
	},

	create: function() {
		var cont = document.createElement('div');
		var colors = this.options.colors;

		// Create swatch table
		var swatchTable = document.createElement('table');
		swatchTable.cellPadding = 0;
		swatchTable.cellSpacing = 0;
		swatchTable.border = 0;
		for (var i = 0; i < 5; ++i) {
			var row = swatchTable.insertRow(i);
			for (var j = 0; j < 8; ++j) {
				var cell = row.insertCell(j);
				var color = colors[(8 * i) + j];
				var swatch = document.createElement('div');
				Element.setStyle(swatch, {'width': '15px', 'height': '15px', 'fontSize': '1px', 'border': '1px solid #EEEEEE', 'backgroundColor': color, 'padding': '0'});
				swatch.onclick = this.swatchClickListener(color);
				swatch.onmouseover = this.swatchHoverListener(color);
				cell.appendChild(swatch);
			}
		}

		// Add spacer row
		/*var spacerRow = swatchTable.insertRow(5);
		var spacerCell = spacerRow.insertCell(0);
		//spacerCell.colSpan = 8;
		spacerCell.colSpan = 8;
		var hr = document.createElement('hr');
		Element.setStyle(hr, {'color': 'gray', 'backgroundColor': 'gray', 'height': '1px', 'border': '0', 'marginTop': '3px', 'marginBottom': '3px', 'padding': '0'});
		spacerCell.appendChild(hr);

		// Add custom color row
		var customRow = swatchTable.insertRow(6);
		var customColors = this.loadSetting('customColors')
			?  this.loadSetting('customColors').split(',')
			: new Array();
		this.customSwatches = [];
		for (var i = 0; i < 8; ++i) {
			var cell = customRow.insertCell(i);
			var color = customColors[i] ? customColors[i] : '#000000';
			var swatch = document.createElement('div');
			Element.setStyle(swatch, {'width': '15px', 'height': '15px', 'fontSize': '15px', 'border': '1px solid #EEEEEE', 'backgroundColor': color, 'padding': '0'});
			cell.appendChild(swatch);
			swatch.onclick = this.swatchCustomClickListener(color, swatch);
			swatch.onmouseover = this.swatchHoverListener(color);
			this.customSwatches.push(swatch);
		}

		// Add spacer row
		spacerRow = swatchTable.insertRow(7);
		spacerCell = spacerRow.insertCell(0);
		spacerCell.colSpan = 8;
		hr = document.createElement('hr');
		Element.setStyle(hr, {'color': 'gray', 'backgroundColor': 'gray', 'height': '1px', 'border': '0', 'marginTop': '3px', 'marginBottom': '3px', 'padding': '0'});
		spacerCell.appendChild(hr);

		// Add custom color entry interface
		var entryRow = swatchTable.insertRow(8);
		var entryCell = entryRow.insertCell(0);
		entryCell.colSpan = 8;
		var entryTable = document.createElement('table');
		entryTable.cellPadding = 0;
		entryTable.cellSpacing = 0;
		entryTable.border = 0;
		entryTable.style.width = '136px';
		entryCell.appendChild(entryTable);

		entryRow = entryTable.insertRow(0);
		var previewCell = entryRow.insertCell(0);
		previewCell.valign = 'bottom';
		var preview = document.createElement('div');
		Element.setStyle(preview, {'width': '15px', 'height': '15px', 'fontSize': '15px', 'border': '1px solid #EEEEEE', 'backgroundColor': '#000000'});
		previewCell.appendChild(preview);
		this.previewSwatch = preview;

		var textboxCell = entryRow.insertCell(1);
		textboxCell.valign = 'bottom';
		textboxCell.align = 'center';
		var textbox = document.createElement('input');
		textbox.type = 'text';
		textbox.value = '#000000';
		Element.setStyle(textbox, {'width': '70px', 'border': '1px solid gray' });
		textbox.onkeyup = function(e) {
				this.previewSwatch.style.backgroundColor = textbox.value;
			}.bindAsEventListener(this);
		textboxCell.appendChild(textbox);
		this.customInput = textbox;

		var submitCell = entryRow.insertCell(2);
		submitCell.valign = 'bottom';
		submitCell.align = 'right';
		var submit = document.createElement('input');
		submit.type = 'button';
		Element.setStyle(submit, {'width': '40px', 'border': '1px solid gray'});
		submit.value = this.options.addLabel;
		submit.onclick = function(e) {
				var idx = 0;
				if (this.activeCustomSwatch) {
					for (var i = 0; i < this.customSwatches.length; ++i)
						if (this.customSwatches[i] == this.activeCustomSwatch) {
							idx = i;
							break;
						}
					this.activeCustomSwatch.style.border = '1px solid #EEEEEE';
					this.activeCustomSwatch = null;
				} else {
					var lastIndex = this.loadSetting('customColorIndex');
					if (lastIndex) idx = (parseInt(lastIndex) + 1) % 8;
				}
				this.saveSetting('customColorIndex', idx);
				customColors[idx] = this.customSwatches[idx].style.backgroundColor = this.customInput.value;
				this.customSwatches[idx].onclick = this.swatchCustomClickListener(customColors[idx], this.customSwatches[idx]);
				this.customSwatches[idx].onmouseover = this.swatchHoverListener(customColors[idx]);
				this.saveSetting('customColors', customColors.join(','));
			}.bindAsEventListener(this);
		submitCell.appendChild(submit);*/

		// Create form
		var swatchForm = document.createElement('form');
		Element.setStyle(swatchForm, {'margin': '0', 'padding': '0'});
		swatchForm.onsubmit = function() {
			if (this.activeCustomSwatch) this.activeCustomSwatch.style.border = '1px solid #EEEEEE';
			this.activeCustomSwatch = null;
			this.editor.setDialogColor(this.customInput.value);
			return false;
		}.bindAsEventListener(this);
		swatchForm.appendChild(swatchTable);

		// Add to dialog window
		cont.appendChild(swatchForm);
		return cont;
	},

	swatchClickListener: function(color) {
		return function(e) {
				if (this.activeCustomSwatch) this.activeCustomSwatch.style.border = '1px solid #EEEEEE';
				this.activeCustomSwatch = null;
				this.options.onSelect(color);
			}.bindAsEventListener(this);
	},

	swatchCustomClickListener: function(color, element) {
		return function(e) {
				if (e.ctrlKey) {
					if (this.activeCustomSwatch) this.activeCustomSwatch.style.border = '1px solid #EEEEEE';
					this.activeCustomSwatch = element;
					this.activeCustomSwatch.style.border = '1px solid #FF0000';
				} else {
					this.activeCustomSwatch = null;
					this.options.onSelect(color);
				}
			}.bindAsEventListener(this);
	},

	swatchHoverListener: function(color) {
		return function(e) {
				this.previewSwatch.style.backgroundColor = color;
				this.customInput.value = color;
			}.bindAsEventListener(this);
	},

	loadSetting: function(name) {
		name = 'colorpicker_' + name;
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	},

	saveSetting: function(name, value, days) {
		name = 'colorpicker_' + name;
		if (!days) days = 180;
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
		document.cookie = name+"="+value+expires+"; path=/";
	},

	clearSetting: function(name) {
		this.saveSetting(name,"",-1);
	}

};
