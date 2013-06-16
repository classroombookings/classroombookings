var alerts = (function($) {
	
	
	var timer = null;
	
	
	var hide_now = function() {
		window.clearTimeout(timer);
		$(".alert.success").remove();
	}
	
	
	var hide_soon = function() {
		if ($(".alert.success").length > 0) {
			timer = window.setTimeout('$(".alert.success").fadeOut("slow");', 5000);
		}
	}
	
	
	var add = function(type, text) {
		hide_now();
		$("<div>").addClass("hidden alert " + type).text(text).appendTo(".flash-container").fadeIn();
		if (type === "success") hide_soon();
	}
	
	
	return {
		error: function(text) { return add("error", text); },
		success: function(text) { return add("success", text); },
		hide_soon: hide_soon
	}
	
	
})(jQuery);


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
	
	// Generic delete buttons
	$(".action-delete").on("click", function(e) {
		
		// Cancel click event
		e.preventDefault();
		
		// Get all data attributes for use in the template
		var delete_data = $(this).data();
		delete_data.csrf = $.cookie('crbscsrf');
		
		// Merge template + data and set dialog container HTML
		var dialog_html = ich.ich_delete(delete_data);
		
		// Set the modal's container's HTML and show it
		$("#delete_dialog")
			.html(dialog_html)
			.modal({
				overlayClose: true,
				opacity: 60,
				minWidth: 500,
				maxWidth: 500,
				minHeight: 200,
				maxHeight: 320,
			});
		
	});
	
	// Button for cancelling/closing the dialog
	$("#delete_dialog").on("click", ".close-dialog", function(e) {
		e.preventDefault();
		$.modal.close();
	});
	
	
	// Handle show/hide filter toggle link
	$("body").on("click", ".toggle-filter", function(e) {
		e.preventDefault();
		var cur_state = $(this).data("state");
		new_state = !cur_state;
		$(this).data("state", new_state);
		
		if (new_state) {
			// Show filter
			$(".filterable.content").removeClass("grid_12").addClass("grid_9");
			$(".filterable.filter").removeClass("hidden").find("input:first").focus();
		} else {
			// Hide filter
			$(".filterable.content").removeClass("grid_9").addClass("grid_12");
			$(".filterable.filter").addClass("hidden");
		}
	});
	$(".toggle-filter").trigger("click");
	
	// Bind forward slash to the show/hide filter toggler
	$(document).bind('keyup', 'f', function(){
		$(".toggle-filter").trigger("click");
	});
	
	// Allow "escaping" focus from an input via esc
	$("input,textarea,select").bind("keyup", "esc", function() {
		$(this).blur();
	});
	
	alerts.hide_soon();
	
	
});
