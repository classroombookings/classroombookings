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
