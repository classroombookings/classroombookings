$.fn.cbtristate = function(settings) {
	
	var config = {
	 	yes: { img: "img/ico/check-yes.png", val: "1" },
		no: { img: "img/ico/check-no.png", val: "0" },
		empty: { img: "img/ico/check-empty.png", val: "" }
	};
	
	
	if (settings) $.extend(config, settings);
	
	var reverse_config = {};
	$.each(config, function(k){
		reverse_config[this.val] = k;
	});
	
	
	this.each(function() {
		
		// Get the initial value to set
		var initial_value = $(this).val();
		var config_key = reverse_config[initial_value];
		
		$(this).bind("change", function(){
			var input = $(this);
			var ref = input.data("ref");
			var destimg = $("img."+ref);
			var state = input.val();
			var imgsrc = null;
			if (state == config.yes.val){
				imgsrc = config.yes.img;
			} else if (state == config.no.val){
				imgsrc = config.no.img;
			} else {
				imgsrc = config.empty.img;
			}
			destimg.attr("src", imgsrc);
		});
		
		
		$(this).bind("tog", function(){
			var state = $(this).val();
			var next = null;
			if (state == config.yes.val){
				next = config.no.val;
			} else if (state == config.no.val){
				next = config.empty.val;
			} else {
				next = config.yes.val;
			}
			$(this).val(next).trigger("change");
		});
		
		
		// Make image for checkbox and insert into DOM next to the input
		var img = $("<img>");
		img.attr("src", config[config_key].img);
		img.addClass("tristate-image");
		img.addClass($(this).data("ref"));
		// 'for' is reference to hidden input's ID
		img.data("for", $(this).attr("id"));
		
		// Toggle function
		img.click(function(){
			var img = $(this);
			// Get hidden input to update
			var destformel = $("input#" + img.data("for"));
			// Get current state from form element
			var state = destformel.val();
			// Switch state
			if (state == config.yes.val){
				next = 'no';
			} else if (state == config.no.val){
				next = 'empty';
			} else {
				next = 'yes';
			}
			// Update image + form element
			//$(this).attr("src", config[next].img);
			destformel.val(config[next].val).trigger("change");
			return false;
		});
		
		img.insertAfter(this);
	
	});		// this.each
	
	
	return this;
	
	
}		// end cbtristate
	 
