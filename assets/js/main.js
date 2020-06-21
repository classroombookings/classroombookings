// up.motion.config.duration = 150;
up.motion.config.enabled = false;

up.compiler('.sort-table', function(element, cols) {
	new SortableTable(element, cols);
});


up.compiler('#bookings_date', function(element) {
	var input = document.getElementById("chosen_date"),
		form = document.getElementById("bookings_date");
	input.addEventListener("change", function(event) {
		form.submit();
	});
});


up.compiler('[ldap-settings]', function(element) {

	function populateTestForm() {
		var attrs = up.Params.fromForm('#ldap_settings'),
			attrsArray = attrs.toArray();
		for (var i = 0; i < attrsArray.length; i++) {
			var sel = "[ldap-settings] [name='" + attrsArray[i]['name'] + "']";
			var dest = "[ldap-test] [name='" + attrsArray[i]['name'] + "']";
			var testEl = up.element.get(dest);
			if (testEl) {
				up.element.setAttrs(testEl, { value: attrsArray[i]['value'] });
			}
		}
	};

	populateTestForm();

	up.observe(element, { batch: true }, function(diff) {
		// console.log('Observed one or more changes: %o', diff)
		populateTestForm();
	});

});
