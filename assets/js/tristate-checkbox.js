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
	
	
	$(this).delegate("span,img", "click", function(e){
		// Get parent label
		var parent = $($(this).closest("label.tristate"));
		// Get the ID attribute from the parent
		var id = parent.data("id");
		// Image to change = this
		var destimg = $(parent.find("span img")[0]);
		// Get form element to update
		var destformel = $(parent.find("input[type=hidden]")[0]);
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
		destimg.attr("src", config[next].img);
		destformel.val(config[next].val);
		return false;
	});
	
	
	this.each(function() {
		
		// Retrieve id from the data attribute.
		// Eventually becomes input name
		var id = $(this).data("id");
		
		// Get the initial value to set
		var initial_value = $(this).data("value");
		var config_key = reverse_config[initial_value];
		
		// Make a container for image + form element
		var container = $("<span>");
		
		// Make image for checkbox and append to container
		var img = $("<img>");
		img.attr("src", config[config_key].img);
		img.appendTo(container);
		
		// Make a hidden form element and append to container
		var formel = $("<input>", {
			type: "hidden",
			name: id,
			value: config[config_key].val
		}).appendTo(container);
		
		// prepend the generated checkbox+form element container to the main parent label element
		container.prependTo(this);
	
	});		// this.each
	
	
	return this;
	
	
}		// end cbtristate
	 
