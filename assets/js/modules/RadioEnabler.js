function RadioEnabler(container) {
	this.radios = container.find('[type="radio"]');
	this.radios.on('click', $.proxy(this, 'onRadioButtonClick'));
	this.setupHtml();
};

RadioEnabler.prototype.setupHtml = function() {
	this.radios.each($.proxy(function(i, el) {
		var targetId = $(el).attr('data-enable')
		if(targetId) {
			$('#'+targetId).prop('disabled', !el.checked);
		}
	}, this));
};

RadioEnabler.prototype.onRadioButtonClick = function(e) {
	this.radios.each($.proxy(function(i, el) {
		var targetId = $(el).attr('data-enable')
		if(targetId) {
			$('#'+targetId).prop('disabled', !el.checked);
		}
	}, this));
};
