Q.push(function() {
	
	// Ensure all POST requests have the CSRF token
	$.ajaxSetup({ data: { crbscsrftoken: $.cookie('crbscsrf') } });
	
	// Attach events for tabs
	$("body").on("click", "dl.htabs > dd > a", function(e) {
		var location = $(this).data("tab");
		if (location) {
			e.preventDefault();
			$(this).closest("dl").find("a.active").removeClass("active");
			$(this).addClass("active");
			$("#" + location + "Tab").closest(".htabs-content").children("li").hide();
			$("#" + location + "Tab").show();
		}
	});
	
});